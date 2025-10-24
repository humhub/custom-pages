<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\controllers;

use humhub\helpers\Html;
use humhub\modules\admin\components\Controller;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\content\models\Content;
use humhub\modules\content\widgets\StateBadge;
use humhub\modules\custom_pages\helpers\Url;
use humhub\modules\custom_pages\models\CustomPage;
use humhub\modules\custom_pages\modules\template\models\forms\AddElementForm;
use humhub\modules\custom_pages\modules\template\models\forms\EditElementForm;
use humhub\modules\custom_pages\modules\template\models\forms\ImportForm;
use humhub\modules\custom_pages\modules\template\models\TemplateSearch;
use humhub\modules\custom_pages\modules\template\models\Template;
use humhub\modules\custom_pages\modules\template\models\TemplateElement;
use humhub\modules\custom_pages\modules\template\services\TemplateExportService;
use humhub\modules\custom_pages\modules\template\widgets\TemplateElementAdminRow;
use humhub\modules\custom_pages\modules\template\widgets\EditElementModal;
use humhub\modules\custom_pages\modules\template\models\forms\EditMultipleElementsForm;
use humhub\modules\custom_pages\modules\template\widgets\EditMultipleElementsModal;
use humhub\modules\custom_pages\modules\template\widgets\TemplateContentTable;
use humhub\modules\custom_pages\modules\template\components\TemplateCache;
use humhub\widgets\bootstrap\Link;
use Yii;
use yii\base\Response;
use yii\data\ActiveDataProvider;
use yii\grid\ActionColumn;
use yii\grid\DataColumn;
use yii\web\NotFoundHttpException;

/**
 * Admin controller for managing templates.
 *
 * @author buddha
 */
class AdminController extends Controller
{
    /**
     * Returns a searchable gridview with all avialable templates of the given type.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new TemplateSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
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

        if ($model === null) {
            $model = new Template(['type' => Template::TYPE_LAYOUT]);
        }

        $model->scenario = 'edit';

        // If the form was submitted try to save/validate and flush the template cache
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            TemplateCache::flushByTemplateId($model->id);
            $this->view->saved();
            return $this->redirect(['edit-source', 'id' => $model->id]);
        }

        return $this->render('edit', ['model' => $model]);
    }

    /**
     * Action used for copying Template instances.
     *
     * @return string result view
     */
    public function actionCopy($id = null)
    {
        $model = Template::findOne(['id' => $id]);

        if ($model == null) {
            throw new NotFoundHttpException(Yii::t('CustomPagesModule.template', 'Template not found!'));
        }

        $model->setOldAttributes(null);
        $model->scenario = 'edit';

        if (!$model->load(Yii::$app->request->post())) {
            $model->name .= ' (Copied)';
        } elseif ($model->saveCopy()) {
            TemplateCache::flushByTemplateId($model->id);
            $this->view->success(Yii::t('CustomPagesModule.template', 'Copied'));
            return $this->redirect(['edit-source', 'id' => $model->id]);
        }

        return $this->render('edit', ['model' => $model]);
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
            throw new NotFoundHttpException(Yii::t('CustomPagesModule.template', 'Template not found!'));
        }

        $model->scenario = 'source';

        // If the form was submitted try to save/validate and flush the template cache
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            TemplateCache::flushByTemplateId($model->id);
            $this->view->saved();
            return $this->redirect(['edit-source', 'id' => $model->id]);
        }

        return $this->render('editSource', [
            'model' => $model,
        ]);
    }

    /**
     * Used to edit resources(CSS & JS) of a template.
     */
    public function actionEditResources()
    {
        $model = Template::findOne(['id' => Yii::$app->request->get('id')]);

        if ($model == null) {
            throw new NotFoundHttpException(Yii::t('CustomPagesModule.template', 'Template not found!'));
        }

        $model->scenario = 'resources';

        // If the form was submitted try to save/validate and flush the template cache
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            TemplateCache::flushByTemplateId($model->id);
            $this->view->saved();
            return $this->redirect(['edit-resources', 'id' => $model->id]);
        }

        return $this->render('editResources', [
            'model' => $model,
        ]);
    }

    /**
     * Show pages/snippets/containers where the template is used in.
     */
    public function actionEditUsage()
    {
        return $this->render('editUsage', $this->getUsageViewOptions());
    }

    /**
     * Show modal window with pages/snippets/containers where the template is used in.
     */
    public function actionEditUsageModal()
    {
        return $this->renderAjax('editUsageModal', $this->getUsageViewOptions());
    }

    private function getUsageViewOptions(): array
    {
        $model = Template::findOne(['id' => Yii::$app->request->get('id')]);

        if ($model == null) {
            throw new NotFoundHttpException(Yii::t('CustomPagesModule.template', 'Template not found!'));
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $model->getLinkedRecordsQuery(),
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        $columns = [];
        $columns[] = [
            'class' => DataColumn::class,
            'label' => $model->type === Template::TYPE_CONTAINER ? Yii::t('CustomPagesModule.base', 'Template')
                : ($model->type === Template::TYPE_SNIPPET_LAYOUT ? Yii::t('CustomPagesModule.base', 'Snippet')
                    : Yii::t('CustomPagesModule.base', 'Page')),
            'format' => 'raw',
            'value' => function ($model) {
                if ($model instanceof Content) {
                    /* @var $record CustomPage */
                    $record = $model->getPolymorphicRelation();
                    return Link::to(Html::encode($record->getTitle()), $record->getUrl())->icon(Html::encode($record->icon)) . ' '
                        . StateBadge::widget(['model' => $record]);
                } elseif ($model instanceof Template) {
                    return $model->name;
                }
                return '';
            },
        ];

        if ($model->type !== Template::TYPE_CONTAINER) {
            $columns[] = [
                'class' => DataColumn::class,
                'label' => Yii::t('CustomPagesModule.base', 'Space'),
                'format' => 'raw',
                'value' => fn($model) => $model instanceof Content && $model->container instanceof ContentContainerActiveRecord
                    ? Html::containerLink($model->container)
                    : '',
            ];
        }

        $columns[] = [
            'class' => ActionColumn::class,
            'options' => ['width' => '80px'],
            'template' => '{update} {delete}',
            'buttons' => [
                'update' => function ($url, $model) {
                    if ($model instanceof Content) {
                        /* @var $record CustomPage */
                        $record = $model->getPolymorphicRelation();
                        return $record->canEdit()
                            ? Link::primary()->icon('pencil')->link($record->getEditUrl())->sm()->right()
                            : '';
                    } elseif ($model instanceof Template) {
                        return Link::primary()->icon('pencil')->link(Url::toRoute(['edit-source', 'id' => $model->id]))->sm();
                    }
                    return '';
                },
                'delete' => fn($url, $model) => $model instanceof Template
                    ? Link::danger()->icon('times')->link(Url::toRoute(['delete-template', 'id' => $model->id]))->sm()->confirm()
                    : '',
            ],
        ];

        return [
            'model' => $model,
            'dataProvider' => $dataProvider,
            'columns' => $columns,
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
            return $this->getJsonEditElementResult(true, TemplateElementAdminRow::widget(['form' => $form]), $form);
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
     * @param int $id
     * @return Response
     */
    public function actionResetElement($id)
    {
        $this->forcePostRequest();

        $element = TemplateElement::findOne(['id' => $id]);
        $elementContent = $element->getDefaultContent();

        if ($elementContent !== null) {
            if ($elementContent->isDefinitionContent()) {
                $element->updateAttributes(['dyn_attributes' => null]);
            }
            $elementContent->delete();
        }

        return $this->asJson([
            'success' => true,
            'message' => Yii::t('CustomPagesModule.template', 'Reset'),
            'id' => $id,
            'output' => $this->renderAjaxPartial(TemplateElementAdminRow::widget(['model' => $element])),
        ]);
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
            'message' => Yii::t('base', 'Saved'),
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
     * @return Response
     * @throws \yii\web\HttpException
     */
    public function actionDeleteElement($id)
    {
        $element = TemplateElement::findOne(['id' => $id]);

        if ($element->template->canEdit()) {
            TemplateCache::flushByTemplateId($element->template_id);
            $result = (bool) $element->delete();
        } else {
            $result = false;
        }

        return $this->asJson([
            'success' => $result,
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

        if ($form->load(Yii::$app->request->post()) && $form->save()) {
            TemplateCache::flushByTemplateId($id);
            return $this->asJson([
                'success' => true,
                'message' => Yii::t('base', 'Saved'),
                'output' => $this->renderAjaxPartial(TemplateContentTable::widget(['template' => $form->template])),
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

        return TemplateExportService::instance($model)->export()->send();
    }

    /**
     * Used to import the source of a template.
     *
     * @return string
     */
    public function actionImportSource()
    {
        $form = new ImportForm();

        if ($form->load(Yii::$app->request->post())) {
            if ($form->import()) {
                $this->view->success(Yii::t('CustomPagesModule.template', 'Imported.'));
                return $this->redirect(['edit-source', 'id' => $form->getService()->template->id]);
            } else {
                $this->view->error(implode(' ', $form->getErrorSummary(true)));
                return $this->redirect(['/custom_pages/template/admin']);
            }
        }

        return $this->renderAjax('importSource', ['model' => $form]);
    }

}
