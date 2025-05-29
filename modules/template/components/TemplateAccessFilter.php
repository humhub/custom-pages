<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2016 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\components;

use humhub\modules\content\helpers\ContentContainerHelper;
use humhub\modules\space\models\Space;
use Yii;
use yii\base\ActionFilter;
use yii\web\ForbiddenHttpException;

/**
 * Manages the access to certain controllers, which are only allowed for admin users (system-admin or space-admin).
 *
 * @author buddha
 */
class TemplateAccessFilter extends ActionFilter
{
    public function beforeAction($action)
    {
        $space = ContentContainerHelper::getCurrent(Space::class);
        if ($space !== null) {
            if (!$space->isAdmin()) {
                throw new ForbiddenHttpException(Yii::t('CustomPagesModule.base', 'Access denied!'));
            }
        } elseif (Yii::$app->user->isGuest || !Yii::$app->user->getIdentity()->isSystemAdmin()) {
            throw new ForbiddenHttpException(403, Yii::t('CustomPagesModule.base', 'Access denied!'));
        }

        return parent::beforeAction($action);
    }
}
