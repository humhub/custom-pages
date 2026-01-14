<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\services;

use humhub\helpers\ControllerHelper;
use humhub\helpers\DeviceDetectorHelper;
use humhub\modules\admin\permissions\ManageModules;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\content\models\Content;
use humhub\modules\custom_pages\helpers\PageType;
use humhub\modules\custom_pages\models\CustomPage;
use humhub\modules\custom_pages\permissions\ManagePages;
use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;
use Yii;

class VisibilityService
{
    public function __construct(protected CustomPage $page)
    {
    }

    /**
     * Check the page has the requested visibility
     *
     * @param int $visibility
     * @return bool
     */
    public function is($visibility): bool
    {
        return in_array($this->page->visibility, func_get_args());
    }

    /**
     * Check the page is visible only for admins
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->is(CustomPage::VISIBILITY_ADMIN) && $this->page->isGlobal();
    }


    /**
     * Check the page is visible only for members
     *
     * @return bool
     */
    public function isPrivate(): bool
    {
        return $this->is(CustomPage::VISIBILITY_PRIVATE);
    }


    /**
     * Check the page is visible for members & guests
     *
     * @return bool
     */
    public function isPublic(): bool
    {
        return $this->is(CustomPage::VISIBILITY_PUBLIC);
    }


    /**
     * Check the page is visible only for guests
     *
     * @return bool
     */
    public function isGuest(): bool
    {
        return $this->is(CustomPage::VISIBILITY_GUEST) && $this->page->isGlobal();
    }


    /**
     * Check the page is visible only for users with specific groups and languages
     *
     * @return bool
     */
    public function isCustom(): bool
    {
        return $this->is(CustomPage::VISIBILITY_CUSTOM);
    }

    /**
     * Check the page is visible only for mobile app users
     *
     * @return bool
     */
    public function isMobileApp(): bool
    {
        return $this->page->visibilityMobileApp;
    }

    public function initDefault(): void
    {
        if ($this->page->visibility === null) {
            // Get first available visibility depending on global settings and the page options
            $this->page->visibility = min(array_keys($this->getOptions()));
        }
    }

    /**
     * Get options for radio list input on edit form
     *
     * @return array
     */
    public function getOptions(): array
    {
        $options = [];

        if ($this->page->isGlobal()) {
            // Global Page
            if (!$this->page->hasTarget(PageType::TARGET_ACCOUNT_MENU)) {
                // For categories except of "User Account Menu (Settings)"
                $options[CustomPage::VISIBILITY_PUBLIC] = Yii::t('CustomPagesModule.base', 'Always');
            }
            $options += [
                CustomPage::VISIBILITY_PRIVATE => Yii::t('CustomPagesModule.base', 'Logged-In Users'),
                CustomPage::VISIBILITY_GUEST => Yii::t('CustomPagesModule.base', 'Non-Logged-In Users'),
                CustomPage::VISIBILITY_ADMIN => Yii::t('CustomPagesModule.base', 'Administrative Users'),
            ];
        } else {
            // Space Page
            if (!$this->page->content->container->isVisibleFor(Space::VISIBILITY_NONE)) {
                // For not private Spaces
                $options[CustomPage::VISIBILITY_PUBLIC] = Yii::t('CustomPagesModule.base', 'Public');
            }
            $options[CustomPage::VISIBILITY_PRIVATE] = Yii::t('CustomPagesModule.base', 'Space Members only');
        }

        $options[CustomPage::VISIBILITY_CUSTOM] = Yii::t('CustomPagesModule.base', 'Custom');

        return $options;
    }

    /**
     * Fix visibility to proper value if current cannot be used depending on other attributes
     */
    public function fix(): void
    {
        if ($this->isPublic() || $this->isGuest()) {
            if ($this->page->getTargetId() == PageType::TARGET_ACCOUNT_MENU) {
                // Force visibility access from "Members & Guests" & "Guests only" to "Members only" for
                // page type "User Account Menu (Settings)"
                $this->page->visibility = CustomPage::VISIBILITY_PRIVATE;
                $this->page->content->visibility = Content::VISIBILITY_PRIVATE;
            } else {
                $this->page->content->visibility = Content::VISIBILITY_PUBLIC;
            }
        } else {
            $this->page->content->visibility = Content::VISIBILITY_PRIVATE;
        }

        // Keep page hidden on stream when "Abstract" field is not filled, or it is visible only for admin
        $this->page->content->hidden = $this->isAdmin() || !$this->page->hasAbstract();
    }

    public function initSettings(): void
    {
        $this->page->visibilityGroups = $this->page->settingService->getValues('group');
        $this->page->visibilityLanguages = $this->page->settingService->getValues('language');
        $this->page->visibilityMobileApp = (bool) $this->page->settingService->get('mobileApp', false);
    }

    public function updateSettings(): void
    {
        if ($this->isCustom()) {
            $this->page->settingService->update('group', $this->page->visibilityGroups);
            $this->page->settingService->update('language', $this->page->visibilityLanguages);
            $this->page->settingService->update('mobileApp', $this->page->visibilityMobileApp);
        }
    }

    public function copySettings(CustomPage $sourcePage): void
    {
        $this->page->visibilityGroups = $sourcePage->visibilityGroups;
        $this->page->visibilityLanguages = $sourcePage->visibilityLanguages;
        $this->page->visibilityMobileApp = $sourcePage->visibilityMobileApp;
    }

    /**
     * Check if the user can view the page
     *
     * @param User|int|string|null $user User instance or user id, null - current user
     * @return bool
     */
    public function canView($user = null): bool
    {
        if (ControllerHelper::isActivePath('file', 'file')
            && Yii::$app->user->can([ManagePages::class])) {
            // Allow to view attached files if user has a permission to manage custom pages
            return true;
        }

        if ($this->isAdmin()) {
            return self::canViewAdminOnlyContent($this->page->content->container);
        }

        if ($this->isGuest()) {
            return Yii::$app->user->isGuest;
        }

        if ($this->isCustom()) {
            if (($this->isMobileApp() && !DeviceDetectorHelper::isAppRequest())
                || !$this->isMobileApp() && DeviceDetectorHelper::isAppRequest()) {
                return false;
            }

            if (!$user && !Yii::$app->user->isGuest) {
                $user = Yii::$app->user->getIdentity();
            } elseif (!$user instanceof User) {
                $user = User::findOne(['id' => $user]);
            }

            if (!$user instanceof User) {
                return false;
            }

            if (!$this->page->settingService->has('language', $user->language)) {
                return false;
            }

            if (!$this->page->isGlobal()) {
                // This Space Page is allowed for the user's language
                return true;
            }

            // Check only Global Page for restriction by the user's group
            $userGroupIds = $user->getGroupUsers()->select('group_id')->column();
            return $this->page->settingService->has('group', $userGroupIds);
        }

        return $this->page->content->canView($user);
    }

    /**
     * Check if the current user can view "Admin only" content from the requested container
     *
     * @param ContentContainerActiveRecord|null $container
     * @return bool
     */
    public static function canViewAdminOnlyContent(?ContentContainerActiveRecord $container = null): bool
    {
        if (Yii::$app->user->isGuest) {
            return false;
        }

        if (!$container) {
            return Yii::$app->user->isAdmin() || Yii::$app->user->can([ManageModules::class, ManagePages::class]);
        }

        if ($container instanceof Space) {
            return $container->isAdmin();
        }

        if ($container instanceof User) {
            $container->is(Yii::$app->user->getIdentity());
        }

        return false;
    }
}
