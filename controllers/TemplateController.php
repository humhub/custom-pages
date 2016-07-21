<?php

namespace humhub\modules\custom_pages\controllers;

use Yii;
use yii\helpers\Url;
use humhub\modules\custom_pages\models\Template;
use humhub\modules\custom_pages\models\forms\AddTemplateBlockForm;
use humhub\modules\custom_pages\models\TemplateBlock;

/**
 * AdminController
 *
 * @author luke
 */
class TemplateController extends \humhub\modules\admin\components\Controller
{

    public function actionIndex()
    {
        $searchModel = new \humhub\modules\custom_pages\models\TemplateSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', array(
                    'dataProvider' => $dataProvider,
                    'searchModel' => $searchModel
        ));
    }

    public function actionEdit()
    {
        $model = Template::findOne(['id' => Yii::$app->request->get('id')]);

        if ($model == null) {
            $model = new Template();
        }

        $model->scenario = 'edit';
        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->save()) {
            return $this->redirect(['edit-source', 'id' => $model->id]);
        }

        return $this->render('edit', ['model' => $model]);
    }

    public function actionEditSource()
    {
        $model = Template::findOne(['id' => Yii::$app->request->get('id')]);

        if ($model == null) {
            throw new \yii\web\HttpException(404, Yii::t('CustomPagesModule.controllers_TemplateController', 'Template not found!'));
        }

        $model->scenario = 'source';
        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('edit_source', [
                    'model' => $model,
                    'contentTypes' => $this->getContentTypes()
        ]);
    }

    private function getContentTypes()
    {
        return [
            \humhub\modules\custom_pages\models\HtmlContent::$label => \humhub\modules\custom_pages\models\HtmlContent::className()
        ];
    }

    public function actionAddTemplateBlock()
    {
        $templateId = Yii::$app->request->get('templateId');
        $type = Yii::$app->request->get('type');
        
        Yii::$app->response->format = 'json';

        if ($type == null || $templateId == null) {
            throw new \yii\web\HttpException(400, Yii::t('CustomPagesModule.controllers_TemplateController', 'Invalid request data!'));
        }

        $form = new AddTemplateBlockForm();
        $form->setContentType($type);
        $form->setTemplateId($templateId);
        $form->action = Url::to(['/custom_pages/template/add-template-block', 'templateId' => $templateId, 'type' => $type]);

        if ($form->load(Yii::$app->request->post()) && $form->validate() && $form->save()) {
            $json = [];
            $json['success'] = true;
            $json['content'] = \humhub\modules\custom_pages\widgets\TemplateBlockAdminRow::widget(['form' => $form]);
            $json['name'] = $form->getName();
            return $json;
        }

        $json = [];
        $json['success'] = false;
        $json['content'] = $this->renderPartial('editTemplateBlockForm', ['model' => $form]);
        $json['name'] = $form->getName();
        return $json;
    }
    
    public function actionEditPageTemplateBlock()
    {
        $pageTemplateId = Yii::$app->request->get('pageTemplateId');
        $blockName = Yii::$app->request->get('name');
        
        Yii::$app->response->format = 'json';
        
        if ($pageTemplateId == null || $blockName == null) {
            throw new \yii\web\HttpException(400, Yii::t('CustomPagesModule.controllers_TemplateController', 'Invalid request data!'));
        }
        
        $form = new \humhub\modules\custom_pages\models\forms\EditPageTemplateBlockForm();
        $form->setPageTemplateBlock($pageTemplateId, $blockName);
        $form->action = Url::to(['/custom_pages/template/edit-page-template-block', 'pageTemplateId' => $pageTemplateId, 'name' => $blockName]);
        
        if ($form->load(Yii::$app->request->post()) && $form->validate() && $form->save()) {
            $json = [];
            $json['success'] = true;
            $json['content'] = $form->templateContent->render();
            $json['name'] = $form->getName();
            return $json;
        }
        
        $json = [];
        $json['success'] = false;
        $json['content'] = $this->renderPartial('editTemplateBlockForm', ['model' => $form]);
        $json['name'] = $form->getName();
        return $json;
    }

    public function actionDeleteTemplateBlock()
    {
        $id = Yii::$app->request->get('id');
        Yii::$app->response->format = 'json';
        
        if ($id == null) {
            throw new \yii\web\HttpException(400, Yii::t('CustomPagesModule.controllers_TemplateController', 'Invalid request data!'));
        }
        
        $block = TemplateBlock::findOne(['id' => $id]);
        $block->delete();
        
        $json = [];
        $json['success'] = true;
        return $json;
    }
}
