<?php

namespace humhub\modules\custom_pages\modules\template\controllers;

use humhub\modules\content\components\ContentContainerController;
use humhub\modules\custom_pages\helpers\Url;
use humhub\modules\custom_pages\modules\template\components\TemplateAccessFilter;
use humhub\modules\custom_pages\modules\template\elements\BaseElementContent;
use humhub\modules\custom_pages\modules\template\elements\ContainerElement;
use humhub\modules\custom_pages\modules\template\elements\ContainerItem;
use humhub\modules\custom_pages\modules\template\models\forms\ImportInstanceForm;
use humhub\modules\custom_pages\modules\template\models\TemplateElement;
use humhub\modules\custom_pages\modules\template\models\TemplateInstance;
use humhub\modules\custom_pages\modules\template\models\forms\AddItemEditForm;
use humhub\modules\custom_pages\modules\template\services\TemplateInstanceExportService;
use humhub\modules\custom_pages\modules\template\widgets\EditContainerItemModal;
use humhub\modules\custom_pages\modules\template\elements\BaseElementVariable;
use humhub\modules\custom_pages\modules\template\models\Template;
use humhub\modules\custom_pages\modules\template\components\TemplateCache;
use humhub\modules\custom_pages\modules\template\widgets\TemplateStructure;
use humhub\modules\space\models\Space;
use Yii;
use yii\base\InvalidRouteException;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * This controller is used to manage ContainerElement and ContainerItem instances.
 *
 * @author buddha
 */
class ContainerContentController extends ContentContainerController
{
    /**
     * @inerhitdoc
     */
    public $requireContainer = false;

    /**
     * @inerhitdoc
     */
    public function behaviors()
    {
        return [
            ['class' => TemplateAccessFilter::class],
        ];
    }

    /**
     * This action is used for empty ContainerElement instances, which means there is only a default ContainerElement for the given owner.
     *
     * This action accepts an $elementContentId, which is the id of the default BaseTemplateElementContent instance,
     * and $templateInstanceId, which defines the actual owner(Custom Page or Container Item) of the element.
     *
     * @param int $elementId
     * @param int $templateInstanceId
     * @param string|null $cguid
     * @return array
     * @throws InvalidRouteException
     * @throws \yii\db\Exception
     */
    public function actionCreateContainer($elementId, $templateInstanceId, $cguid = null)
    {
        // Load actual template instance and default content
        $templateInstance = TemplateInstance::findOne(['id' => $templateInstanceId]);
        if (!$templateInstance) {
            throw new NotFoundHttpException('Template instance is not found!');
        }

        if (!$this->isValidTemplateInstance($templateInstance)) {
            throw new BadRequestHttpException('Invalid template instance!');
        }

        // Check if element content already exists
        $elementContent = BaseElementContent::findOne([
            'element_id' => $elementId,
            'template_instance_id' => $templateInstance->id,
        ]);

        // If there is no container content yet, we create an ElementContent instance by copying the default one.
        if (!$elementContent) {
            // Create a copy of the default content
            $element = TemplateElement::findOne(['id' => $elementId]);
            if (!$element) {
                throw new NotFoundHttpException('Template element is not found!');
            }
            $elementContent = $element->getDefaultContent(true)->copy();
            $elementContent->template_instance_id = $templateInstance->id;
            $elementContent->save();
        }

        return $this->runAction('add-item', [
            'elementContentId' => $elementContent->id,
            'elementContent' => $elementContent,
            'cguid' => $cguid,
        ]);
    }

    /**
     * This action is used to add new ContainerItems to an element.
     *
     * This action accepts either an $elementContentId or an $elementContent instance of ContainerElement.
     *
     * Note: The given elementContent has to be the actual ContainerElement and not a default BaseTemplateElementContent.
     *
     * @param int $elementContentId id of actual ContainerElement
     * @param ContainerElement|null $elementContent actual (non default) BaseTemplateElementContent instance
     * @param string|null $cguid
     * @return mixed|Response|null
     * @throws ForbiddenHttpException
     * @throws InvalidRouteException
     */
    public function actionAddItem($elementContentId, $elementContent = null, $cguid = null)
    {
        if ($elementContent === null) {
            $elementContent = ContainerElement::findOne(['id' => $elementContentId]);
        }

        if (!$elementContent || !$elementContent->canAddItem()) {
            throw new ForbiddenHttpException(Yii::t('CustomPagesModule.base', 'This container does not allow any further items!'));
        }

        if (!$this->isValidTemplateInstance($elementContent->templateInstance)) {
            throw new BadRequestHttpException('Invalid template instance!');
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
    protected function getAllowedTemplateSelection($containerElementContent): array
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
     * - ContainerElement - provided either as $elementContentId or $elementContent instance.
     * - Template - provided as post/get templateId or as $itemTemplate instance.
     *
     * @param int|null $elementContentId id of the actual ContainerElement instance.
     * @param ContainerElement|null $elementContent instance of the actual Container Element Content.
     * @param int|null $templateId item template id.
     * @param Template|null $itemTemplate Template instance of the itemt template.
     * @param string|null $cguid
     * @return Response
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
     */
    public function actionEditAddItem($elementContentId = null, $elementContent = null, $templateId = null, $itemTemplate = null, $cguid = null)
    {
        // First do some validation of the given data
        if ($elementContentId == null && $elementContent == null) {
            throw new BadRequestHttpException('This action requires an elementContentId or elementContent instance!');
        }

        if ($elementContent === null) {
            $elementContent = ContainerElement::findOne(['id' => $elementContentId]);
        }

        if (!$elementContent->canAddItem()) {
            throw new ForbiddenHttpException(Yii::t('CustomPagesModule.base', 'This container does not allow any further items!'));
        }

        if (!$this->isValidTemplateInstance($elementContent->templateInstance)) {
            throw new BadRequestHttpException('Invalid template instance!');
        }

        if ($itemTemplate == null && $templateId == null && Yii::$app->request->post('templateId') == null) {
            throw new BadRequestHttpException('This action requires an templateId or template instance!');
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

        if ($form->load(Yii::$app->request->post()) && $form->save()) {
            TemplateCache::flushByElementContent($elementContent);
            return $this->asJson([
                'success' => true,
                'id' => $elementContent->id,
                'output' => $elementContent->getTemplateVariable(),
                'structure' => TemplateStructure::widget(['templateInstance' => $form->owner->templateInstance]),
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
     * This action is used to delete container items.
     *
     * @return Response
     */
    public function actionDeleteItem()
    {
        $this->forcePostRequest();
        $itemId = Yii::$app->request->post('itemId');
        $elementContentId = Yii::$app->request->post('elementContentId');

        $item = ContainerItem::findOne(['id' => $itemId]);
        if (!$item) {
            throw new NotFoundHttpException('Item is not found!');
        }

        if (!$this->isValidTemplateInstance($item->templateInstance)) {
            throw new BadRequestHttpException('Invalid template instance!');
        }

        $item->delete();
        $elementContent = BaseElementContent::findOne(['id' => $elementContentId]);

        TemplateCache::flushByElementContent($elementContent);

        return $this->asJson([
            'success' => true,
            'output' => $elementContent->getTemplateVariable(),
        ]);
    }

    /**
     * Action for moving a Container Item position.
     *
     * @param int $elementContentId
     * @param int $itemId
     * @param int $step
     * @return Response
     * @throws BadRequestHttpException
     */
    public function actionMoveItem($elementContentId, $itemId, $step)
    {
        $elementContent = BaseElementContent::findOne(['id' => $elementContentId]);

        if ($elementContent === null) {
            throw new BadRequestHttpException(Yii::t('CustomPagesModule.base', 'Invalid request data!'));
        }

        if (!$this->isValidTemplateInstance($elementContent->templateInstance)) {
            throw new BadRequestHttpException('Invalid template instance!');
        }

        $elementContent->moveItem($itemId, $step);

        TemplateCache::flushByElementContent($elementContent);

        return $this->asJson([
            'success' => true,
            'output' => $elementContent->getTemplateVariable(),
        ]);
    }

    public function actionExportInstance()
    {
        $instance = $this->getTemplateInstance();

        $elementId = Yii::$app->request->get('elementId');
        if ($elementId > 0) {
            $element = TemplateElement::findOne(['id' => $elementId]);
            if ($element === null || $element->template_id !== $instance->template_id) {
                throw new NotFoundHttpException(Yii::t('CustomPagesModule.template', 'Template element is not found!'));
            }
        }

        return TemplateInstanceExportService::instance($instance, $element ?? null)
            ->export()
            ->send();
    }

    public function actionImportInstance()
    {
        $instance = $this->getTemplateInstance();

        $elementId = Yii::$app->request->get('elementId');
        if ($elementId > 0) {
            $element = TemplateElement::findOne(['id' => $elementId]);
            if ($element === null || $element->template_id !== $instance->template_id) {
                throw new NotFoundHttpException(Yii::t('CustomPagesModule.template', 'Template element is not found!'));
            }
        }

        $form = new ImportInstanceForm([
            'instance' => $instance,
            'element' => $element ?? null,
        ]);

        if ($form->load(Yii::$app->request->post()) && $form->import()) {
            $this->view->success(Yii::t('CustomPagesModule.template', 'Imported.'));
            return $this->redirect(Url::toInlineEdit($instance->page, $this->contentContainer));
        }

        return $this->asJson([
            'output' => $this->renderAjax('importTemplateInstance', ['model' => $form]),
        ]);
    }

    private function getTemplateInstance(): TemplateInstance
    {
        $instance = TemplateInstance::findOne(['id' => Yii::$app->request->get('id')]);

        if ($instance === null) {
            throw new NotFoundHttpException(Yii::t('CustomPagesModule.template', 'Template instance is not found!'));
        }

        if (!$this->isValidTemplateInstance($instance)) {
            throw new BadRequestHttpException('Invalid template instance!');
        }

        return $instance;
    }

    /**
     * Check if the requested template instance is linked to current Space or Global
     *
     * @param TemplateInstance $instance
     * @return bool
     */
    private function isValidTemplateInstance(TemplateInstance $instance): bool
    {
        $instanceContainer = $instance->page?->content?->container;

        return !($instanceContainer instanceof Space) || // Global template instance
            $instanceContainer->is($this->contentContainer); // Space template instance
    }

}
