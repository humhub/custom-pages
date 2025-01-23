<?php

namespace humhub\modules\custom_pages\modules\template\controllers;

use humhub\components\Controller;
use humhub\modules\custom_pages\modules\template\elements\ContainerElement;
use humhub\modules\custom_pages\modules\template\elements\ContainerItem;
use Yii;
use yii\helpers\Url;
use humhub\modules\custom_pages\modules\template\models\forms\AddItemEditForm;
use humhub\modules\custom_pages\modules\template\widgets\EditContainerItemModal;
use humhub\modules\custom_pages\modules\template\models\forms\EditItemForm;
use humhub\modules\custom_pages\modules\template\models\OwnerContent;
use humhub\modules\custom_pages\modules\template\models\OwnerContentVariable;
use humhub\modules\custom_pages\modules\template\models\Template;
use humhub\modules\custom_pages\modules\template\components\TemplateCache;

/**
 * This controller is used to manage ContainerElement and ContainerItem instances.
 *
 * @author buddha
 */
class ContainerContentController extends Controller
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
     * This action is used for empty ContainerElement instances, which means there is only a default ContainerElement for the given owner.
     *
     * This action accepts an $ownerContentId, which is the id of the default OwnerContent instance,
     * and an owner definition ($ownerModel, $ownerId), which defines the actual owner of the element.
     *
     * @param string $ownerModel
     * @param int $ownerId
     * @param int $ownerContentId
     * @return array
     * @throws \yii\base\InvalidRouteException
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
     * This action is used to add new ContainerItems to an element.
     *
     * This action accepts either an $ownerContentId or an $ownerContent instance of type OwnerContent.
     *
     * Note: The given ownerContent has to be the actual OwnerContent and not a default OwnerContent.
     *
     * @param int $ownerContentId id of actual ownerContent
     * @param OwnerContent $ownerContent actual (non default) ownerContent instance
     * @return mixed|\yii\web\Response
     * @throws \yii\web\HttpException
     * @throws \yii\base\InvalidRouteException
     */
    public function actionAddItem($elementContentId, $elementContent = null, $cguid = null)
    {
        if ($elementContent === null) {
            $elementContent = ContainerElement::findOne(['id' => $elementContentId]);
        }

        if (!$elementContent || !$elementContent->canAddItem()) {
            throw new \yii\web\HttpException(403, Yii::t('CustomPagesModule.base', 'This container does not allow any further items!'));
        }

        // If the ContentContainerDefinition only allows one specific template, we skip the template selection.
        if ($elementContent->isSingleAllowedTemplate()) {
            return $this->runAction('edit-add-item', [
                'templateId' => $elementContent->allowedTemplates[0]->id,
                'elementContent' => $elementContent,
                'cguid' => $cguid,
            ]);
        }

        return $this->asJson([
            'output' => $this->renderAjax('addItemChooseTemplateModal', [
                'allowedTemplateSelection' => $this->getAllowedTemplateSelection($elementContent),
                'action' => Url::to(['edit-add-item', 'elementContentId' => $elementContentId, 'cguid' => $cguid]),
            ]),
        ]);
    }

    /**
     * Creates a selection array in form of 'template.id' => 'template.name' for all allowed Templates of the
     * given ContainerElement instance.
     *
     * @param ContainerElement $containerElementContent
     * @return array
     */
    protected function getAllowedTemplateSelection($containerElementContent)
    {
        $result = [];
        foreach ($containerElementContent->definition->allowedTemplates as $allowedTemplate) {
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
     * @param int $ownerContentId id of the actual OwnerContent instance.
     * @param type $ownerContent instance of the actual OwnerContent.
     * @param int $templateId item template id.
     * @param type $itemTemplate Template instance of the itemt template.
     * @return \yii\web\Response
     * @throws \yii\web\HttpException
     */
    public function actionEditAddItem($elementContentId = null, $elementContent = null, $templateId = null, $itemTemplate = null, $cguid = null)
    {
        // First do some validation of the given data
        if ($elementContentId == null && $elementContent == null) {
            throw new \yii\web\HttpException(400, Yii::t('CustomPagesModule.base', 'This action requires an ownerContentId or ownerContent instance!'));
        }

        if ($elementContent === null) {
            $elementContent = ContainerElement::findOne(['id' => $elementContentId]);
        }

        if (!$elementContent->canAddItem()) {
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
        $form = new AddItemEditForm(['elementContent' => $elementContent]);
        $form->setItemTemplate($itemTemplate);
        $form->setScenario('edit');

        if (Yii::$app->request->post() && $form->load(Yii::$app->request->post()) && $form->save()) {
            // TemplateCache::flushByOwnerContent($ownerContent);
            $variable = new OwnerContentVariable(['elementContent' => $elementContent]);
            return $this->asJson([
                'success' => true,
                'id' => $elementContent->id,
                'output' => $variable->render(true),
            ]);
        }

        return $this->asJson([
            'output' => $this->renderAjaxPartial(EditContainerItemModal::widget([
                'model' => $form,
                'title' => Yii::t('CustomPagesModule.base', '<strong>Add</strong> {templateName} item', ['templateName' => $form->template->name]),
                'action' => Url::to(['edit-add-item', 'elementContentId' => $elementContent->id, 'templateId' => $itemTemplate->id, 'cguid' => $cguid]),
            ])),
        ]);
    }

    /**
     * This action is used to edit an container item.
     *
     * @throws \Exception
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
                'output' => $form->owner->render(true, $form->owner->container->definition->is_inline),
            ]);
        }

        return $this->asJson([
            'output' => $this->renderAjaxPartial(EditContainerItemModal::widget([
                'model' => $form,
                'title' => Yii::t('CustomPagesModule.base', '<strong>Edit</strong> item'),
            ])),
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

        ContainerItem::findOne(['id' => $itemId])->delete();
        $ownerContent = OwnerContent::findOne(['id' => $ownerContentId]);
        $variable = new OwnerContentVariable(['ownerContent' => $ownerContent]);

        TemplateCache::flushByOwnerContent($ownerContent);

        return $this->asJson([
            'success' => true,
            'output' => $variable->render(true),
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
            throw new \yii\web\HttpException(400, Yii::t('CustomPagesModule.base', 'Invalid request data!'));
        }

        $ownerContent->instance->moveItem($itemId, $step);

        TemplateCache::flushByOwnerContent($ownerContent);

        $variable = new OwnerContentVariable(['ownerContent' => $ownerContent]);
        return [
            'success' => true,
            'output' => $variable->render(true),
        ];
    }

}
