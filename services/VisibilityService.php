<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\services;

use humhub\modules\content\models\Content;
use humhub\modules\custom_pages\helpers\PageType;
use humhub\modules\custom_pages\models\CustomPage;
use humhub\modules\space\models\Space;
use humhub\modules\user\helpers\AuthHelper;
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

    public function isAdmin(): bool
    {
        return $this->is(CustomPage::VISIBILITY_ADMIN);
    }

    public function isPrivate(): bool
    {
        return $this->is(CustomPage::VISIBILITY_PRIVATE);
    }

    public function isPublic(): bool
    {
        return $this->is(CustomPage::VISIBILITY_PRIVATE);
    }

    public function isGuest(): bool
    {
        return $this->is(CustomPage::VISIBILITY_GUEST);
    }

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
        // Force visibility access from "Members & Guests" to "Members only" for
        // page type "User Account Menu (Settings)"
        if ($this->isPublic() && $this->page->getTargetId() == PageType::TARGET_ACCOUNT_MENU) {
            $this->page->visibility = CustomPage::VISIBILITY_PRIVATE;
        }

        if ($this->isPublic() || $this->isGuest()) {
            $this->page->content->visibility = Content::VISIBILITY_PUBLIC;
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
}
