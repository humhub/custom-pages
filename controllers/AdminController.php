<?php

namespace humhub\modules\custom_pages\controllers;

use Yii;
use humhub\modules\custom_pages\models\Page;
use humhub\modules\custom_pages\models\AddPageForm;

/**
 * AdminController
 *
 * @author luke
 */
class AdminController extends \humhub\modules\admin\components\Controller
{

    public function actionIndex()
    {
        $pages = Page::find()->all();
        return $this->render('list', array('pages' => $pages));
    }

    public function actionAdd()
    {
        $model = new AddPageForm;
        $model->availableTypes = Page::getPageTypes();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            return $this->redirect(['edit', 'type' => $model->type]);
        }

        return $this->render('add', ['model' => $model]);
    }

    public function actionEdit()
    {
        $page = Page::findOne(['id' => Yii::$app->request->get('id')]);

        if ($page === null) {
            $page = new Page;
            $page->type = (int) Yii::$app->request->get('type');
        }

        if ($page->load(Yii::$app->request->post()) && $page->validate() && $page->save()) {
            if ($page->type == Page::TYPE_MARKDOWN) {
                \humhub\modules\file\models\File::attachPrecreated($page, Yii::$app->request->post('fileUploaderHiddenGuidField'));
            }
            
            return $this->redirect(['index']);
        }

        return $this->render('edit', ['page' => $page]);
    }

    public function actionDelete()
    {
        $page = Page::findOne(['id' => Yii::$app->request->get('id')]);

        if ($page !== null) {
            $page->delete();
        }

        return $this->redirect(['index']);
    }

}
