<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2016 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\components;

use humhub\modules\custom_pages\modules\template\helpers\PagePermissionHelper;
use yii\base\ActionFilter;
use yii\web\ForbiddenHttpException;

/**
 * Manages the access to certain controllers, which are only allowed for admin users (system-admin or space-admin).
 *
 * @author buddha
 */
class TemplateAccessFilter extends ActionFilter
{
    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if (!PagePermissionHelper::canEdit()) {
            throw new ForbiddenHttpException('Access denied!');
        }

        return parent::beforeAction($action);
    }
}
