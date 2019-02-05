<?php

namespace humhub\modules\custom_pages\modules\template\controllers;

use Yii;
use yii\helpers\Url;
use humhub\modules\custom_pages\modules\template\models\forms\AddItemEditForm;
use humhub\modules\custom_pages\modules\template\widgets\EditContainerItemModal;
use humhub\modules\custom_pages\modules\template\models\forms\EditItemForm;
use humhub\modules\custom_pages\modules\template\models\ContainerContentItem;
use humhub\modules\custom_pages\modules\template\models\OwnerContent;
use humhub\modules\custom_pages\modules\template\models\OwnerContentVariable;
use humhub\modules\custom_pages\modules\template\models\Template;
use humhub\modules\custom_pages\modules\template\components\TemplateCache;

/**
 * This controller is used to manage ContainerContent and ContainerContentItem instances.
 *
 * @author buddha
 */
class ContainerContentController extends \humhub\components\Controller
{

    /**
     * @inerhitdoc
     */
    public function behaviors()
    {
        return [
            ['class' => 'humhub\modules\custom_pages\modules\template\components\TemplateAccessFilter'],
        ];
    }

    /**
     * This action is used for empty ContainerContent instances, which means there is only a default CotnainerContent for the given owner.
     * 
     * This action accepts an $ownerContentId, which is the id of the default OwnerContent instance,
     * and an owner definition ($ownerModel, $ownerId), which defines the actual owner of the element.
     * 
     * @param string $ownerModel
     * @param integer $ownerId
     * @param integer $ownerContentId
     * @return array
     */
    public function actionCreateContainer($ownerModel, $ownerId, $ownerContentId, $cguid = null)
    {
        // Load actual owner and default content
        $owner = OwnerContent::getOwnerModel($ownerModel, $ownerId);
        $defaultOwnerContent = OwnerContent::findOne(['id' => $ownerContentId]);

        // Check if owner content already exists
        $ownerContent = OwnerContent::findOne(['owner_model' => $ownerModel, 'owner_id' => $owner->id, 'element_name' => $defaultOwnerContent->element_name]);

        // If there is no container content yet, we create an OwnerContent isntance by copying the default one.
        if (!$ownerContent) {
            // Create a copy of the default content
            $content = $defaultOwnerContent->copyContent();
            $content->save();

            // Copy default OwnerContent and set owner and new content
            $ownerContent = $defaultOwnerContent->copy();
            $ownerContent->setOwner($owner);
            $ownerContent->setContent($content);
            $ownerContent->save();
        }

        return $this->runAction('add-item', ['ownerContentId' => $ownerContent->id, 'ownerContent' => $ownerContent, 'cguid' => $cguid]);
    }

    /**
     * This action is used to add new ContainerContentItems to an element.
     * 
     * This action accepts either an $ownerContentId or an $ownerContent instance of type OwnerContent.
     * 
     * Note: The given ownerContent has to be the actual OwnerContent and not a default OwnerContent.
     * 
     * @param integer $ownerContentId id of actual ownerContent
     * @param OwnerContent $ownerContent actual (non default) ownerContent instance
     * @throws \yii\web\HttpException
     */
    public function actionAddItem($ownerContentId, $ownerContent = null, $cguid = null)
    {
        $ownerContent = (!$ownerContent) ? OwnerContent::findOne(['id' => $ownerContentId]) : $ownerContent;

        if (!$ownerContent->instance->canAddItem()) {
            throw new \yii\web\HttpException(403, Yii::t('CustomPagesModule.base', 'This container does not allow any further items!'));
        }

        // If the ContentContainerDefinition only allows one specific template, we skip the template selection.
        if ($ownerContent->instance->isSingleAllowedTemplate()) {
            return $this->runAction('edit-add-item', [
                        'templateId' => $ownerContent->instance->allowedTemplates[0]->id,
                        'ownerContent' => $ownerContent,
                        'cguid' => $cguid
            ]);
        }

        return $this->asJson([
                    'output' => $this->renderAjax('addItemChooseTemplateModal', [
                        'allowedTemplateSelection' => $this->getAllowedTemplateSelection($ownerContent->instance),
                        'action' => Url::to(['edit-add-item', 'ownerContentId' => $ownerContentId, 'cguid' => $cguid])
                    ])
        ]);
    }

    /**
     * Creates a selection array in form of 'template.id' => 'template.name' for all allowed Templates of the
     * given ContainerContent instance.
     * 
     * @param \humhub\modules\custom_pages\modules\template\models\ContainerContent $containerContent 
     * @return array
     */
    protected function getAllowedTemplateSelection($containerContent)
    {
        $result = [];
        foreach ($containerContent->definition->allowedTemplates as $allowedTemplate) {
            $result[$allowedTemplate->id] = $allowedTemplate->name;
        }

        return $result;
    }

    /**
     * This action handles the second step of the add item process and is responsible
     * for rendering and handling the item edit form.
     * 
     * This function requires an 
     * 
     * - OwnerContent - provided either as $ownerContentId or $ownerContent instance.
     * - Template - provided as post/get templateId or as $itemTemplate instance.
     * 
     * 
     * @param integer $ownerContentId id of the actual OwnerContent instance.
     * @param type $ownerContent instance of the actual OwnerContent.
     * @param integer $templateId item template id.
     * @param type $itemTemplate Template instance of the itemt template.
     * @throws \yii\web\HttpException
     */
    public function actionEditAddItem($ownerContentId = null, $ownerContent = null, $templateId = null, $itemTemplate = null, $cguid = null)
    {
        // First do some validation of the given data
        if ($ownerContentId == null && $ownerContent == null) {
            throw new \yii\web\HttpException(400, Yii::t('CustomPagesModule.base', 'This action requires an ownerContentId or ownerContent instance!'));
        }

        $ownerContent = ($ownerContent == null) ? OwnerContent::findOne(['id' => $ownerContentId]) : $ownerContent;

        if (!$ownerContent->instance->canAddItem()) {
            throw new \yii\web\HttpException(403, Yii::t('CustomPagesModule.base', 'This container does not allow any further items!'));
        }

        if ($itemTemplate == null && $templateId == null && Yii::$app->request->post('templateId') == null) {
            throw new \yii\web\HttpException(400, Yii::t('CustomPagesModule.base', 'This action requires an templateId or template instance!'));
        }

        // Initialize the itemTemplate
        if (!$itemTemplate) {
            $templateId = ($templateId === null) ? Yii::$app->request->post('templateId') : $templateId;
            $itemTemplate = Template::find()->where(['custom_pages_template.id' => $templateId])->joinWith('elements')->one();
        }

        // Render form or handle form submission
        $form = new AddItemEditForm(['ownerContent' => $ownerContent]);
        $form->setItemTemplate($itemTemplate);
        $form->setScenario('edit');

        if (Yii::$app->request->post() && $form->load(Yii::$app->request->post()) && $form->save()) {
            TemplateCache::flushByOwnerContent($ownerContent);
            $variable = new OwnerContentVariable(['ownerContent' => $ownerContent]);
            return $this->asJson([
                        'success' => true,
                        'id' => $ownerContent->id,
                        'output' => $variable->render(true)
            ]);
        }

        return $this->asJson([
                    'output' => EditContainerItemModal::widget([
                        'model' => $form,
                        'title' => Yii::t('CustomPagesModule.controllers_AdminController', '<strong>Add</strong> {templateName} item', ['templateName' => $form->template->name]),
                        'action' => Url::to(['edit-add-item', 'ownerContentId' => $ownerContent->id, 'templateId' => $itemTemplate->id, 'cguid' => $cguid])
                    ])
        ]);
    }

    /**
     * This action is used to edit an container item.
     *
     * @throws \yii\web\HttpException
     */
    public function actionEditItem($itemId)
    {
        $form = new EditItemForm();
        $form->setItem($itemId);
        $form->setScenario('edit');

        if (Yii::$app->request->post() && $form->load(Yii::$app->request->post()) && $form->save()) {
            $ownerContent = OwnerContent::findByContent($form->owner->container);
            TemplateCache::flushByOwnerContent($ownerContent);

            return $this->asJson([
                        'success' => true,
                        'output' => $form->owner->render(true, $form->owner->container->definition->is_inline)
            ]);
        }

        return $this->asJson([
                    'output' => EditContainerItemModal::widget(['model' => $form,
                        'title' => Yii::t('CustomPagesModule.controllers_AdminController', '<strong>Edit</strong> item')])
        ]);
    }

    /**
     * This action is used to delete container items.
     */
    public function actionDeleteItem()
    {
        $this->forcePostRequest();
        $itemId = Yii::$app->request->post('itemId');
        $ownerContentId = Yii::$app->request->post('ownerContentId');

        ContainerContentItem::findOne(['id' => $itemId])->delete();
        $ownerContent = OwnerContent::findOne(['id' => $ownerContentId]);
        $variable = new OwnerContentVariable(['ownerContent' => $ownerContent]);

        TemplateCache::flushByOwnerContent($ownerContent);

        return $this->asJson([
            'success' => true,
            'output' => $variable->render(true)
        ]);
    }

    /**
     * Action for moving an containeritem position.
     * @throws \yii\web\HttpException
     */
    public function actionMoveItem($ownerContentId, $itemId, $step)
    {
        Yii::$app->response->format = 'json';
        $ownerContent = OwnerContent::findOne(['id' => $ownerContentId]);

        if ($ownerContent == null) {
            throw new \yii\web\HttpException(400, Yii::t('CustomPagesModule.controllers_TemplateController', 'Invalid request data!'));
        }

        $ownerContent->instance->moveItem($itemId, $step);

        TemplateCache::flushByOwnerContent($ownerContent);

        $variable = new OwnerContentVariable(['ownerContent' => $ownerContent]);
        return [
            'success' => true,
            'output' => $variable->render(true)
        ];
    }

}
