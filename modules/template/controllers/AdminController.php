<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\controllers;

use Yii;
use humhub\modules\custom_pages\modules\template\models\Template;
use humhub\modules\custom_pages\modules\template\models\forms\AddElementForm;
use humhub\modules\custom_pages\modules\template\models\forms\EditElementForm;
use humhub\modules\custom_pages\modules\template\models\TemplateElement;
use humhub\modules\custom_pages\modules\template\widgets\TemplateElementAdminRow;
use humhub\modules\custom_pages\modules\template\widgets\EditElementModal;
use humhub\modules\custom_pages\modules\template\models\forms\EditMultipleElementsForm;
use humhub\modules\custom_pages\modules\template\widgets\EditMultipleElementsModal;
use humhub\modules\custom_pages\modules\template\widgets\TemplateContentTable;
use humhub\modules\custom_pages\modules\template\components\TemplateCache;

/**
 * Description of AbstractAdminController
 *
 * @author buddha
 */
class AdminController extends \humhub\modules\admin\components\Controller
{

    public $type;
    public $indexHelp;

    public function actionIndex()
    {
        $searchModel = new \humhub\modules\custom_pages\modules\template\models\TemplateSearch(['type' => $this->type]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('@custom_pages/modules/template/views/admin/index', [
                    'helpText' => $this->indexHelp,
                    'type' => $this->type,
                    'dataProvider' => $dataProvider,
                    'searchModel' => $searchModel
        ]);
    }

    /**
     * Action used for creating and editing Template instances.
     * 
     * @return string result view
     */
    public function actionEdit()
    {
        $model = Template::findOne(['id' => Yii::$app->request->get('id')]);

        if ($model == null) {
            $model = new Template(['type' => $this->type]);
        }

        $model->scenario = 'edit';
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            TemplateCache::flushByTemplateId($model->id);
            Yii::$app->getSession()->setFlash('data-saved', Yii::t('CustomPagesModule.base', 'Saved'));
            return $this->redirect(['edit-source', 'id' => $model->id]);
        }

        return $this->render('@custom_pages/modules/template/views/admin/edit', ['model' => $model]);
    }

    public function actionEditSource()
    {
        $model = Template::findOne(['id' => Yii::$app->request->get('id')]);

        if ($model == null) {
            throw new \yii\web\HttpException(404, Yii::t('CustomPagesModule.modules_template_controllers_AdminController', 'Template not found!'));
        }

        $model->scenario = 'source';

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            TemplateCache::flushByTemplateId($model->id);
            Yii::$app->getSession()->setFlash('data-saved', Yii::t('CustomPagesModule.base', 'Saved'));
            return $this->redirect(['edit-source', 'id' => $model->id]);
        }

        return $this->render('@custom_pages/modules/template/views/admin/editSource', [
                    'model' => $model,
                    'contentTypes' => $this->getContentTypes()
        ]);
    }

    public function actionAddElement()
    {
        $templateId = Yii::$app->request->get('template_id');
        $contentType = Yii::$app->request->get('content_type');

        if ($contentType == null || $templateId == null) {
            throw new \yii\web\HttpException(400, Yii::t('CustomPagesModule.modules_template_controllers_AdminController', 'Invalid request data!'));
        }

        Yii::$app->response->format = 'json';

        $form = new AddElementForm();
        $form->setElementDefinition($templateId, $contentType);
        $form->setScenario('create');
        
        if ($form->load(Yii::$app->request->post()) && $form->save()) {
            TemplateCache::flushByTemplateId($templateId);
            return $this->getJsonEditElementResult(true, TemplateElementAdminRow::widget(['form' => $form, 'saved' => true]), $form);
        }

        $result = EditElementModal::widget([
                    'model' => $form,
                    'title' => Yii::t('CustomPagesModule.modules_template_controllers_AdminController', '<strong>Add</strong> new {type} element', ['type' => $form->getLabel()])
        ]);

        return $this->getJsonEditElementResult(false, $result, $form);
    }
    
    public function actionEditElement()
    {
        $elementId = Yii::$app->request->get('id');
        
        if ($elementId == null) {
            throw new \yii\web\HttpException(400, Yii::t('CustomPagesModule.modules_template_controllers_AdminController', 'Invalid request data!'));
        }
        
        Yii::$app->response->format = 'json';
         
        $form = new EditElementForm();
        $form->setElementId($elementId);
        $form->setScenario('edit-admin');
        
        if($form->load(Yii::$app->request->post()) && $form->save()) {
            TemplateCache::flushByTemplateId($form->element->template_id);
            return $this->getJsonEditElementResult(true, TemplateElementAdminRow::widget(['form' => $form, 'saved' => true]), $form);
        }
        
        $result = EditElementModal::widget([
            'model' => $form,
            'title' => Yii::t('CustomPagesModule.modules_template_controllers_AdminController', '<strong>Edit</strong> element')
        ]);
        
        return $this->getJsonEditElementResult(false, $result, $form);
    }
    
    private function getJsonEditElementResult($success, $content, $form)
    {
        $json = [];
        $json['success'] = $success;
        $json['content'] = $content;
        $json['name'] = $form->element->name;
        $json['id'] = $form->element->id;
        return $json;
    }

    public function actionDeleteTemplate()
    {
        $id = Yii::$app->request->get('id');
        $template = Template::findOne(['id' => $id]);
        
        if(!$template->delete()) {
            Yii::$app->session->setFlash('error', 
                Yii::t('CustomPagesModule.modules_template_controllers_AdminController', 'The template could not be deleted, please get sure that this template is not in use.'));
        
        }
        
        return $this->redirect(['index']);
    }
    
    public function actionDeleteElement()
    {
        $id = Yii::$app->request->get('id');
        Yii::$app->response->format = 'json';

        if ($id == null) {
            throw new \yii\web\HttpException(400, Yii::t('CustomPagesModule.modules_template_controllers_AdminController', 'Invalid request data!'));
        }
        
        if(Yii::$app->request->post('confirmation')) {
            $element = TemplateElement::findOne(['id' => $id]);
            TemplateCache::flushByTemplateId($element->template_id);
            $element->delete();
            
            return [
                'success' => true,
                'id' => $id
            ];
        }
        
        return [
            'success' => true,
            'content' => \humhub\modules\custom_pages\modules\template\widgets\ConfirmDeletionModal::widget([
                'title' => Yii::t('CustomPagesModule.modules_template_controller_OwnerContentController', '<strong>Confirm</strong> element deletion'),
                'message' => Yii::t('CustomPagesModule.modules_template_widgets_views_confirmDeletionModal', 'Do you really want to delete this element? <br />The deletion will affect all pages using this template.'),
            ])
        ];
    }

    public function actionEditMultiple()
    {
        Yii::$app->response->format = 'json';
        $templateId = Yii::$app->request->get('id');

        if ($templateId == null) {
            throw new \yii\web\HttpException(400, Yii::t('CustomPagesModule.modules_template_controllers_AdminController', 'Invalid request data!'));
        }

        $form = new EditMultipleElementsForm(['scenario' => 'edit-admin']);
        $form->setOwnerTemplateId($templateId);

        if (Yii::$app->request->post() && $form->load(Yii::$app->request->post()) && $form->save()) {
            TemplateCache::flushByTemplateId($templateId);
            return [
                'success' => true,
                'content' => TemplateContentTable::widget(['template' => $form->template, 'saved' => true])
            ];
        }

        return [
            'success' => false,
            'content' => EditMultipleElementsModal::widget([
                'model' => $form,
                'title' => Yii::t('CustomPagesModule.modules_template_controllers_AdminController', '<strong>Edit</strong> elements of {templateName}', ['templateName' => $form->template->name])
            ])
        ];
    }

    private function getContentTypes()
    {
        return [
            \humhub\modules\custom_pages\modules\template\models\TextContent::$label => \humhub\modules\custom_pages\modules\template\models\TextContent::className(),
            \humhub\modules\custom_pages\modules\template\models\RichtextContent::$label => \humhub\modules\custom_pages\modules\template\models\RichtextContent::className(),
            \humhub\modules\custom_pages\modules\template\models\ImageContent::$label => \humhub\modules\custom_pages\modules\template\models\ImageContent::className(),
            \humhub\modules\custom_pages\modules\template\models\ContainerContent::$label => \humhub\modules\custom_pages\modules\template\models\ContainerContent::className(),
        ];
    }
    
    public function actionInfo()
    {
        return $this->renderPartial('@custom_pages/modules/template/views/admin/info');
    }

}
