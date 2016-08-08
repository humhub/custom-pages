<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2016 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\components;

use Yii;
use yii\base\ActionFilter;

class TemplateAccessFilter extends ActionFilter
{

    public function beforeAction($action)
    {
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

    public function afterAction($action, $result)
    {
        return parent::afterAction($action, $result);
    }
    
    public function checkSpacePermission($suid)
    {
        
    }
}