<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\services;

use humhub\modules\admin\permissions\ManageModules;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\content\models\Content;
use humhub\modules\custom_pages\helpers\PageType;
use humhub\modules\custom_pages\models\CustomPage;
use humhub\modules\custom_pages\permissions\ManagePages;
use humhub\modules\space\models\Space;
use humhub\modules\user\helpers\AuthHelper;
use humhub\modules\user\models\User;
use Yii;

class VisibilityService
{
    protected CustomPage $page;

    public function __construct(CustomPage $page)
    {
        $this->page = $page;
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
        return $this->is(CustomPage::VISIBILITY_ADMIN);
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
        return $this->is(CustomPage::VISIBILITY_GUEST);
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
        $options = [
            CustomPage::VISIBILITY_ADMIN => Yii::t('CustomPagesModule.base', 'Admin only'),
        ];

        if ($this->page->isGlobal()) {
            $options[CustomPage::VISIBILITY_PRIVATE] = Yii::t('CustomPagesModule.base', 'Members only');
            if (AuthHelper::isGuestAccessEnabled()) {
                if ($this->page->getTargetId() != PageType::TARGET_ACCOUNT_MENU) {
                    $options[CustomPage::VISIBILITY_PUBLIC] = Yii::t('CustomPagesModule.base', 'Members & Guests');
                }
            } else {
                $options[CustomPage::VISIBILITY_PUBLIC] = Yii::t('CustomPagesModule.base', 'All Members');
            }
        } else {
            $options[CustomPage::VISIBILITY_PRIVATE] = Yii::t('CustomPagesModule.base', 'Space Members only');

            if ($this->page->content->container->visibility != Space::VISIBILITY_NONE) {
                $options[CustomPage::VISIBILITY_PUBLIC] = Yii::t('CustomPagesModule.base', 'Public');
            }
        }

        $options[CustomPage::VISIBILITY_GUEST] = Yii::t('CustomPagesModule.base', 'Guests only');
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

    public function loadAdditionalOptions(): void
    {
        if ($this->isCustom()) {
            $this->page->visibility_groups = $this->page->settingService->getAll('group');
            $this->page->visibility_languages = $this->page->settingService->getAll('language');
        }
    }

    public function updateAdditionalOptions(): void
    {
        if ($this->isCustom()) {
            $this->page->settingService->update('group', $this->page->visibility_groups);
            $this->page->settingService->update('language', $this->page->visibility_languages);
        }
    }

    /**
     * Check if the user can view the page
     *
     * @param User|int|string|null $user User instance or user id, null - current user
     * @return bool
     */
    public function canView($user = null): bool
    {
        if ($this->isAdmin()) {
            return self::canViewAdminOnlyContent($this->page->content->container);
        }

        if ($this->isGuest()) {
            return Yii::$app->user->isGuest;
        }

        if ($this->isCustom()) {
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
