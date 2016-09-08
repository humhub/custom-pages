<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace humhub\modules\custom_pages\modules\template\models;

use Yii;

/**
 * This is the model class for table "custom_pages_template".
 */
class TemplatePagePermission
{
    public static function canEdit()
    {
        if(isset(Yii::$app->controller->contentContainer)) {
            return Yii::$app->controller->contentContainer->isAdmin();
        } else {
            return !Yii::$app->user->isGuest && Yii::$app->user->getIdentity()->isSystemAdmin();
        }
    }
    public static function canTemplate()
    {
        return !Yii::$app->user->isGuest && Yii::$app->user->getIdentity()->isSystemAdmin();
    }
    
}

