<?php

namespace humhub\modules\custom_pages\modules\template\controllers;

use Yii;
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

    public function behaviors()
    {
        return [
            [
                'class' => 'humhub\modules\custom_pages\modules\template\components\TemplateAccessFilter'
            ],
        ];
    }

    /**
     * This action is used for empty ContainerContent instances, which means there is only
     * a default CotnainerContent for the given owner.
     * 
     * This action accepts an $ownerContentId, which is the id of the default OwnerContent instance,
     * and an owner definition ($ownerModel, $ownerId), which defines the actual owner of the element.
     * 
     * @param string $ownerModel
     * @param integer $ownerId
     * @param integer $ownerContentId
     * @return array
     */
    public function actionCreateContainer($ownerModel, $ownerId, $ownerContentId, $sguid = null)
    {
        Yii::$app->response->format = 'json';

        // Load actual owner and default content
        $owner = OwnerContent::getOwnerModel($ownerModel, $ownerId);
        $defaultOwnerContent = OwnerContent::findOne(['id' => $ownerContentId]);

        // Check if owner content already exists
        $ownerContent = OwnerContent::findOne(['owner_model' => $ownerModel, 'owner_id' => $owner->id, 'element_name' => $defaultOwnerContent->element_name]);     
                
        if($ownerContent == null) {
            // Copy default ContainerContent and save
            $content = $defaultOwnerContent->copyContent();

            $content->save();

            // Copy default OwnerContent and set owner and new content
            $ownerContent = $defaultOwnerContent->copy();
            $ownerContent->setOwner($owner);
            $ownerContent->setContent($content);
            $ownerContent->save();
        }
        // Forward to AddItem action
        return $this->runAction('add-item', ['ownerContentId' => $ownerContent->id, 'ownerContent' => $ownerContent, 'sguid' => $sguid]);
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
     * @return array
     * @throws \yii\web\HttpException
     */
    public function actionAddItem($ownerContentId, $ownerContent = null, $sguid = null)
    {
        Yii::$app->response->format = 'json';

        $ownerContent = ($ownerContent == null) ? OwnerContent::findOne(['id' => $ownerContentId]) : $ownerContent;

        if (!$ownerContent->instance->canAddItem()) {
            throw new \yii\web\HttpException(403, Yii::t('CustomPagesModule.base', 'This container does not allow any further items!'));
        }

        // If the ContentContainerDefinition only allows one template, we skip the template selection.
        if ($ownerContent->instance->isSingleAllowedTemplate()) {
            return $this->runAction('edit-add-item', [
                        'templateId' => $ownerContent->instance->allowedTemplates[0],
                        'ownerContent' => $ownerContent
            ]);
        }

        return [
            'success' => false,
            'content' => $this->renderPartial('addItemChooseTemplateModal', [
                'allowedTemplateSelection' => $this->getAllowedTemplateSelection($ownerContent->instance),
                'action' => \yii\helpers\Url::to(['edit-add-item', 'ownerContentId' => $ownerContentId, 'sguid' => $sguid])
            ])
        ];
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
     * @return array
     * @throws \yii\web\HttpException
     */
    public function actionEditAddItem($ownerContentId = null, $ownerContent = null, $templateId = null, $itemTemplate = null, $sguid = null)
    {
        Yii::$app->response->format = 'json';

        // First do some validation of the given data
        if ($ownerContentId == null && $ownerContent == null) {
            throw new \yii\web\HttpException(400, Yii::t('CustomPagesModule.base', 'This action requires an ownerContentId or ownerContent instance!'));
        }

        $ownerContent = ($ownerContent == null) ? OwnerContent::findOne(['id' => $ownerContentId]) : $ownerContent;

        if (!$ownerContent->instance->canAddItem()) {
            throw new \yii\web\HttpException(403, Yii::t('CustomPagesModule.base', 'This container does not allow any further items!'));
        }

        if ($itemTemplate == null && $templateId == null && Yii::$app->request->post('templateId') == null) {
            throw new \yii\web\HttpException(400, Yii::t('CustomPagesModule.base', 'This action requres an templateId or template instance!'));
        }

        // Initialize the itemTemplate
        if ($itemTemplate == null && $templateId == null) {
            $itemTemplate = Template::find()->where(['custom_pages_template.id' => Yii::$app->request->post('templateId')])->joinWith('elements')->one();
        } else if ($itemTemplate == null) {
            $itemTemplate = Template::find()->where(['custom_pages_template.id' => $templateId])->joinWith('elements')->one();
        }

        // Render form or handle form submission
        $form = new AddItemEditForm(['ownerContent' => $ownerContent]);
        $form->setItemTemplate($itemTemplate);
        $form->setScenario('edit');

        if (Yii::$app->request->post() && $form->load(Yii::$app->request->post()) && $form->save()) {
            TemplateCache::flushByOwnerContent($ownerContent);
            $variable = new OwnerContentVariable(['ownerContent' => $ownerContent]);
            return [
                'success' => true,
                'id' => $ownerContent->id,
                'content' => $variable->render(true)
            ];
        }

        return [
            'success' => false,
            'content' => EditContainerItemModal::widget([
                'model' => $form,
                'title' => Yii::t('CustomPagesModule.controllers_AdminController', '<strong>Add</strong> {templateName} item', ['templateName' => $form->template->name]),
                'action' => \yii\helpers\Url::to(['edit-add-item', 'ownerContentId' => $ownerContent->id, 'templateId' => $itemTemplate->id, 'sguid' => $sguid])
            ])
        ];
    }

    public function actionEditItem()
    {
        Yii::$app->response->format = 'json';
        $itemId = Yii::$app->request->get('itemId');

        if ($itemId == null) {
            throw new \yii\web\HttpException(400, Yii::t('CustomPagesModule.controllers_TemplateController', 'Invalid request data!'));
        }

        $form = new EditItemForm();
        $form->setItem($itemId);
        $form->setScenario('edit');
        if (Yii::$app->request->post() && $form->load(Yii::$app->request->post()) && $form->save()) {
            $ownerContent = OwnerContent::findByContent($form->owner->container);
            TemplateCache::flushByOwnerContent($ownerContent);
            return [
                'success' => true,
                'content' => $form->owner->render(true, $form->owner->container->definition->is_inline)
            ];
        }

        return [
            'success' => false,
            'content' => EditContainerItemModal::widget([
                'model' => $form,
                'title' => Yii::t('CustomPagesModule.controllers_AdminController', '<strong>Edit</strong> container item')
            ])
        ];
    }

    public function actionDeleteItem()
    {
        Yii::$app->response->format = 'json';

        $itemId = Yii::$app->request->get('itemId');
        $ownerContentId = Yii::$app->request->get('ownerContentId');

        if ($itemId == null) {
            throw new \yii\web\HttpException(400, Yii::t('CustomPagesModule.controllers_TemplateController', 'Invalid request data!'));
        }

        if (Yii::$app->request->post('confirmation')) {
            ContainerContentItem::findOne(['id' => $itemId])->delete();
            $ownerContent = OwnerContent::findOne(['id' => $ownerContentId]);
            $variable = new OwnerContentVariable(['ownerContent' => $ownerContent]);
            
            TemplateCache::flushByOwnerContent($ownerContent);
            
            return [
                'success' => true,
                'content' => $variable->render(true)
            ];
        }

        return [
            'success' => true,
            'content' => \humhub\modules\custom_pages\modules\template\widgets\ConfirmDeletionModal::widget([
                'title' => Yii::t('CustomPagesModule.modules_template_controller_OwnerContentController', '<strong>Confirm</strong> container item deletion'),
                'message' => Yii::t('CustomPagesModule.modules_template_widgets_views_confirmDeletionModal', 'Are you sure you want to delete this container item?'),
                'successEvent' => 'itemDeleteSuccess'
            ])
        ];
    }

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
            'content' => $variable->render(true)
        ];
    }

}
