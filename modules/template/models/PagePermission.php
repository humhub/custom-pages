<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace humhub\modules\custom_pages\modules\template\models;

use humhub\modules\content\helpers\ContentContainerHelper;
use humhub\modules\space\models\Space;
use Yii;

/**
 * This is the model class for table "custom_pages_template".
 */
class PagePermission
{
    public static function canEdit()
    {
        if(Yii::$app->user->isGuest) {
            return false;
        }

        $container = ContentContainerHelper::getCurrent();
        if($container instanceof Space) {
            return $container->isAdmin();
        }

        return Yii::$app->user->isAdmin();
    }

    public static function canTemplate()
    {
        return !Yii::$app->user->isGuest && Yii::$app->user->isAdmin();
    }
    
}

