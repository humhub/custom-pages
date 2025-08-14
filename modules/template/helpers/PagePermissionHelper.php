<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\helpers;

use humhub\modules\admin\permissions\ManageModules;
use humhub\modules\content\helpers\ContentContainerHelper;
use humhub\modules\custom_pages\permissions\ManagePages;
use humhub\modules\space\models\Space;
use Yii;

class PagePermissionHelper
{
    public static function canEdit(): bool
    {
        static $canEdit;

        if ($canEdit === null) {
            if (Yii::$app->user->isGuest) {
                $canEdit = false;
            } elseif ($space = ContentContainerHelper::getCurrent(Space::class)) {
                $canEdit = $space->isAdmin();
            } else {
                $canEdit = Yii::$app->user->isAdmin() || Yii::$app->user->can([ManageModules::class, ManagePages::class]);
            }
        }

        return $canEdit;
    }

    public static function canTemplate(): bool
    {
        return !Yii::$app->user->isGuest && Yii::$app->user->isAdmin();
    }
}
