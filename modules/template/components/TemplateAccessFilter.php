<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2016 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\components;

use humhub\modules\custom_pages\modules\template\controllers\ContainerContentController;
use humhub\modules\custom_pages\modules\template\controllers\ElementContentController;
use humhub\modules\custom_pages\modules\template\elements\ContainerElement;
use humhub\modules\custom_pages\modules\template\helpers\PagePermissionHelper;
use humhub\modules\custom_pages\modules\template\models\TemplateInstance;
use Yii;
use yii\base\Action;
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
        if (!PagePermissionHelper::canEdit($this->getTemplateInstance($action)?->page)) {
            throw new ForbiddenHttpException('Access denied!');
        }

        return parent::beforeAction($action);
    }

    /**
     * Get Template Instance depending on the current request
     * It is required to allow the edit permission for Editors(users who can edit only content of Custom Page)
     *
     * @param Action $action
     * @return TemplateInstance|null
     */
    private function getTemplateInstance(Action $action): ?TemplateInstance
    {
        if ($action->controller instanceof ElementContentController) {
            return match ($action->id) {
                'edit-multiple' => TemplateInstance::findOne(['id' => Yii::$app->request->get('id')]),
                'delete-by-content' => ContainerElement::findOne(['id' => Yii::$app->request->post('elementContentId')])?->templateInstance,
                default => null,
            };
        }

        if ($action->controller instanceof ContainerContentController) {
            return match ($action->id) {
                'create-container',
                'add-item' => TemplateInstance::findOne(['id' => Yii::$app->request->get('templateInstanceId')]),
                'edit-add-item',
                'move-item' => ContainerElement::findOne(['id' => Yii::$app->request->get('elementContentId')])?->templateInstance,
                'delete-item' => ContainerElement::findOne(['id' => Yii::$app->request->post('elementContentId')])?->templateInstance,
                'export-instance',
                'import-instance' => TemplateInstance::findOne(['id' => Yii::$app->request->get('id')]),
                default => null,
            };
        }

        return null;
    }
}
