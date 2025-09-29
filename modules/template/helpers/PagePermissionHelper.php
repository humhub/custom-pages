<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\helpers;

use humhub\modules\admin\permissions\ManageModules;
use humhub\modules\content\helpers\ContentContainerHelper;
use humhub\modules\custom_pages\models\CustomPage;
use humhub\modules\custom_pages\permissions\ManagePages;
use humhub\modules\space\models\Space;
use Yii;

class PagePermissionHelper
{
    public static function canEdit(?CustomPage $page = null): bool
    {
        static $canEdit = [];

        $pageId = $page?->id ?? 0;

        if (!array_key_exists($pageId, $canEdit)) {
            if (Yii::$app->user->isGuest) {
                $canEdit[$pageId] = false;
            } elseif ($space = ContentContainerHelper::getCurrent(Space::class)) {
                $canEdit[$pageId] = $space->isAdmin() || $page?->canEdit() || $page?->isEditor();
            } else {
                $canEdit[$pageId] = Yii::$app->user->isAdmin()
                    || Yii::$app->user->can([ManageModules::class, ManagePages::class])
                    || $page?->canEdit()
                    || $page?->isEditor();
            }
        }

        return $canEdit[$pageId];
    }

    public static function canTemplate(): bool
    {
        return !Yii::$app->user->isGuest && Yii::$app->user->isAdmin();
    }
}
