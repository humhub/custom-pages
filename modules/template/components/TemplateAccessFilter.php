<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2016 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\components;

use Yii;
use yii\base\ActionFilter;

/**
 * Manages the access to certains controller, which are only allowed for admin users (system-admin or space-admin).
 * 
 * @author buddha
 */
class TemplateAccessFilter extends ActionFilter
{

    public function beforeAction($action)
    {
        // If a sguid is present, we only grand access to space admins, otherwise we expect an system admin user.
        if(Yii::$app->request->get('sguid')) {
            $space = \humhub\modules\space\models\Space::findOne(['guid' => Yii::$app->request->get('sguid')]);
            
            if(!$space->isAdmin()) {
                 throw new \yii\web\HttpException(403, Yii::t('CustomPagesModule.controllers_TemplateController', 'Access denied!'));
            }
        } else if(Yii::$app->user->isGuest || !Yii::$app->user->getIdentity()->isSystemAdmin()) {
            throw new \yii\web\HttpException(403, Yii::t('CustomPagesModule.controllers_TemplateController', 'Access denied!'));
        }
        
        return parent::beforeAction($action);
    }
}