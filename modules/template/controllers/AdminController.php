<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\controllers;

use humhub\modules\custom_pages\modules\template\elements\FileDownloadElement;
use humhub\modules\custom_pages\modules\template\elements\FileElement;
use humhub\modules\custom_pages\modules\template\elements\HumHubRichtextElement;
use humhub\modules\custom_pages\modules\template\elements\ImageElement;
use humhub\modules\custom_pages\modules\template\elements\RichtextElement;
use humhub\modules\custom_pages\modules\template\elements\RssElement;
use humhub\modules\custom_pages\modules\template\elements\SpaceElement;
use humhub\modules\custom_pages\modules\template\elements\SpacesElement;
use humhub\modules\custom_pages\modules\template\elements\TextElement;
use humhub\modules\custom_pages\modules\template\elements\UserElement;
use humhub\modules\custom_pages\modules\template\elements\UsersElement;
use humhub\modules\custom_pages\modules\template\models\ContainerContent;
use humhub\modules\custom_pages\modules\template\models\forms\AddElementForm;
use humhub\modules\custom_pages\modules\template\models\forms\EditElementForm;
use humhub\modules\custom_pages\modules\template\models\forms\ImportForm;
use humhub\modules\custom_pages\modules\template\models\TemplateSearch;
use humhub\modules\custom_pages\modules\template\models\Template;
use humhub\modules\custom_pages\modules\template\models\TemplateElement;
use humhub\modules\custom_pages\modules\template\services\ExportService;
use humhub\modules\custom_pages\modules\template\widgets\TemplateElementAdminRow;
use humhub\modules\custom_pages\modules\template\widgets\EditElementModal;
use humhub\modules\custom_pages\modules\template\models\forms\EditMultipleElementsForm;
use humhub\modules\custom_pages\modules\template\widgets\EditMultipleElementsModal;
use humhub\modules\custom_pages\modules\template\widgets\TemplateContentTable;
use humhub\modules\custom_pages\modules\template\components\TemplateCache;
use Yii;
use yii\base\Response;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

/**
 * Admin controller for managing templates.
 *
 * This controller is designed to support different template types by setting the $type
 * and $indexHelp attributes.
 *
 * @author buddha
 */
class AdminController extends \humhub\modules\admin\components\Controller
{
    /**
     * Defines the template type this controller should manage.
     *
     * @var type
     */
    public $type;

    /**
     * Defines the index help text for the given template type.
     * @var type
     */
    public $indexHelp;

    /**
     * Returns a searchable gridview with all avialable templates of the given type.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new TemplateSearch(['type' => $this->type]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('@custom_pages/modules/template/views/admin/index', [
            'helpText' => $this->indexHelp,
            'type' => $this->type,
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Action used for creating and editing Template instances.
     *
     * @return string result view
     */
    public function actionEdit($id = null)
    {
        $model = Template::findOne(['id' => $id]);

        if ($model == null) {
            $model = new Template(['type' => $this->type]);
        }

        $model->scenario = 'edit';

        // If the form was submitted try to save/validate and flush the template cache
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            TemplateCache::flushByTemplateId($model->id);
            Yii::$app->getSession()->setFlash('data-saved', Yii::t('CustomPagesModule.base', 'Saved'));
            return $this->redirect(['edit-source', 'id' => $model->id]);
        }

        return $this->render('@custom_pages/modules/template/views/admin/edit', ['model' => $model]);
    }

    /**
     * Used to edit the source of a template.
     *
     * @return string
     * @throws \yii\web\HttpException
     */
    public function actionEditSource()
    {
        $model = Template::findOne(['id' => Yii::$app->request->get('id')]);

        if ($model == null) {
            throw new \yii\web\HttpException(404, Yii::t('CustomPagesModule.template', 'Template not found!'));
        }

        $model->scenario = 'source';

        // If the form was submitted try to save/validate and flush the template cache
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            TemplateCache::flushByTemplateId($model->id);
            Yii::$app->getSession()->setFlash('data-saved', Yii::t('CustomPagesModule.base', 'Saved'));
            return $this->redirect(['edit-source', 'id' => $model->id]);
        }

        return $this->render('@custom_pages/modules/template/views/admin/editSource', [
            'model' => $model,
            'contentTypes' => $this->getContentTypes(),
        ]);
    }

    /**
     * Show pages/snippets/containers where the template is used in.
     */
    public function actionEditUsage()
    {
        $model = Template::findOne(['id' => Yii::$app->request->get('id')]);

        if ($model == null) {
            throw new \yii\web\HttpException(404, Yii::t('CustomPagesModule.template', 'Template not found!'));
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $model->getLinkedRecordsQuery(),
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        return $this->render('@custom_pages/modules/template/views/admin/editUsage', [
            'model' => $model,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Returns a selection of all available template content types.
     * @return array
     */
    private function getContentTypes()
    {
        return [
            TextElement::$label => TextElement::class,
            RichtextElement::$label => RichtextElement::class,
            HumHubRichtextElement::$label => HumHubRichtextElement::class,
            ImageElement::$label => ImageElement::class,
            FileElement::$label => FileElement::class,
            FileDownloadElement::$label => FileDownloadElement::class,
            ContainerContent::$label => ContainerContent::class,
            RssElement::$label => RssElement::class,
            UserElement::$label => UserElement::class,
            SpaceElement::$label => SpaceElement::class,
            UsersElement::$label => UsersElement::class,
            SpacesElement::$label => SpacesElement::class,
        ];
    }

    /**
     * Used to add elements to a template.
     *
     * @return Response
     * @throws \yii\web\HttpException
     */
    public function actionAddElement($templateId, $type)
    {
        $form = new AddElementForm();
        $form->setElementDefinition($templateId, $type);
        $form->setScenario('create');

        // If the form was submitted try to save/validate and flush the template cache
        if ($form->load(Yii::$app->request->post()) && $form->save()) {
            TemplateCache::flushByTemplateId($templateId);
            return $this->getJsonEditElementResult(true, TemplateElementAdminRow::widget(['form' => $form]), $form);
        }

        $result = $this->renderAjaxPartial(EditElementModal::widget([
            'model' => $form,
            'isAdminEdit' => true,
            'title' => Yii::t('CustomPagesModule.template', '<strong>Add</strong> new {type} element', ['type' => $form->getLabel()]),
        ]));

        return $this->getJsonEditElementResult(false, $result, $form);
    }

    /**
     * Used to edit the element of a template.
     *
     * This controller expects an id request parameter.
     *
     * @return Response
     * @throws \yii\web\HttpException if no template id was given.
     */
    public function actionEditElement($id)
    {
        $form = new EditElementForm();
        $form->setElementId($id);
        $form->setScenario('edit-admin');

        if ($form->load(Yii::$app->request->post()) && $form->save()) {
            TemplateCache::flushByTemplateId($form->element->template_id);
            return $this->getJsonEditElementResult(true, TemplateElementAdminRow::widget(['form' => $form, 'saved' => true]), $form);
        }

        $result = $this->renderAjaxPartial(EditElementModal::widget([
            'model' => $form,
            'isAdminEdit' => true,
            'title' => Yii::t('CustomPagesModule.template', '<strong>Edit</strong> element {name}', ['name' => $form->element->name]),
            'resetUrl' => \yii\helpers\Url::to(['reset-element', 'id' => $id]),
        ]));

        return $this->getJsonEditElementResult(false, $result, $form);
    }

    /**
     * This action will reset the default content of a given TemplateElement
     * @param type $id
     * @return type
     */
    public function actionResetElement($id)
    {
        $this->forcePostRequest();

        $element = TemplateElement::findOne(['id' => $id]);
        $ownerContent = $element->getDefaultContent();

        if ($ownerContent != null) {
            $ownerContent->delete();
        }

        return $this->asJson([
            'success' => true,
            'id' => $id,
            'output' => $this->renderAjaxPartial(TemplateElementAdminRow::widget(['model' => $element, 'saved' => true])),
        ]);
    }

    /**
     * This action will render a preview of a given template.
     *
     * @param type $id
     * @param type $editView
     * @param type $reload
     * @return type
     */
    public function actionPreview($id, $editView = null, $reload = null)
    {
        $this->subLayout = null;
        $template = Template::findOne(['id' => $id]);

        $editView = ($editView != null) ? $editView : false;

        if ($reload != null) {
            return $this->renderPartial('@custom_pages/modules/template/views/admin/preview', [
                'template' => $template,
                'editView' => $editView,
            ]);
        } else {
            return $this->render('@custom_pages/modules/template/views/admin/preview', [
                'template' => $template,
                'editView' => $editView,
            ]);
        }
    }

    /**
     * Creates a json result array used by multiple actions.
     *
     * @param bool $success defines if the process was successfull e.g. saving an element
     * @param mixed $content content result
     * @param mixed $form Form model
     * @return Response
     */
    private function getJsonEditElementResult($success, $content, $form)
    {
        return $this->asJson([
            'success' => $success,
            'output' => $content,
            'name' => $form->element->name,
            'id' => $form->element->id,
        ]);
    }

    /**
     * Will delete a given template model instance.
     *
     * @return type
     */
    public function actionDeleteTemplate($id)
    {
        $template = Template::findOne(['id' => $id]);

        if ($template) {
            if ($template->delete()) {
                $this->view->success(Yii::t('CustomPagesModule.base', 'Deleted.'));
            } else {
                $this->view->error(Yii::t('CustomPagesModule.template', 'The template could not be deleted, please get sure that this template is not in use.'));
            }
        }

        return $this->redirect(['index']);
    }

    /**
     * Will delete an template element model instance.
     *
     * This action requres a confirmation.
     *
     * @return type
     * @throws \yii\web\HttpException
     */
    public function actionDeleteElement($id)
    {
        $element = TemplateElement::findOne(['id' => $id]);
        TemplateCache::flushByTemplateId($element->template_id);
        $element->delete();

        $this->asJson([
            'success' => true,
            'id' => $id,
        ]);
    }

    /**
     * This action is used to edit multiple elements.
     *
     * @return Response
     * @throws \yii\web\HttpException
     */
    public function actionEditMultiple($id)
    {
        $form = new EditMultipleElementsForm(['scenario' => 'edit-admin']);
        $form->setOwnerTemplateId($id);

        if (Yii::$app->request->post() && $form->load(Yii::$app->request->post()) && $form->save()) {
            TemplateCache::flushByTemplateId($id);
            return $this->asJson([
                'success' => true,
                'output' => $this->renderAjaxPartial(TemplateContentTable::widget(['template' => $form->template, 'saved' => true])),
            ]);
        }

        return $this->asJson([
            'success' => false,
            'output' => $this->renderAjaxPartial(EditMultipleElementsModal::widget([
                'model' => $form,
                'title' => Yii::t('CustomPagesModule.template', '<strong>Edit</strong> {templateName}', ['templateName' => $form->template->name]),
            ])),
        ]);
    }

    /**
     * Returns an info text view.
     * @return string
     */
    public function actionInfo()
    {
        return $this->renderPartial('@custom_pages/modules/template/views/admin/info');
    }

    /**
     * Used to export the source of a template.
     *
     * @return Response
     */
    public function actionExportSource()
    {
        $model = Template::findOne(['id' => Yii::$app->request->get('id')]);

        if ($model === null) {
            throw new NotFoundHttpException(Yii::t('CustomPagesModule.template', 'Template not found!'));
        }

        return ExportService::instance($model)->export()->send();
    }

    /**
     * Used to import the source of a template.
     *
     * @return string
     */
    public function actionImportSource(string $type)
    {
        $form = new ImportForm(['type' => $type]);

        if ($form->load(Yii::$app->request->post())) {
            if ($form->import()) {
                $this->view->success(Yii::t('CustomPagesModule.template', 'Imported.'));
                return $this->redirect(['edit-source', 'id' => $form->getService()->template->id]);
            } else {
                $this->view->error(implode(' ', $form->getErrorSummary(true)));
                return $this->redirect(['/custom_pages/template/' . $type . '-admin']);
            }
        }

        return $this->renderAjax('@custom_pages/modules/template/views/admin/importSource', ['model' => $form]);
    }

}
