<?php

namespace humhub\modules\custom_pages\controllers;

use Yii;
use humhub\modules\custom_pages\models\Page;
use humhub\modules\custom_pages\components\Container;
use humhub\modules\custom_pages\models\AddPageForm;
use humhub\modules\file\models\File;

/**
 * AdminController
 *
 * @author luke, buddha
 */
class AdminController extends \humhub\modules\admin\components\Controller
{

    public function actionIndex()
    {
        return $this->render('@custom_pages/views/common/list', [
                    'pages' => $this->findAll(),
                    'label' => Yii::createObject($this->getPageClassName())->getLabel(),
                    'subNav' => \humhub\modules\custom_pages\widgets\AdminMenu::widget()
        ]);
    }

    public function actionAdd()
    {
        $model = new AddPageForm(['class' => $this->getPageClassName(), 'isAdmin' => true]);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            return $this->redirect(['edit', 'type' => $model->type]);
        }

        return $this->render('@custom_pages/views/common/add', [
                    'model' => $model,
                    'subNav' => \humhub\modules\custom_pages\widgets\AdminMenu::widget()
        ]);
    }

    public function actionEdit($type = null, $id = null)
    {
        $page = $this->findByid($id);

        if ($page === null) {
            $page = Yii::createObject($this->getPageClassName());
            $page->type = $type;
        }

        if ($page->load(Yii::$app->request->post()) && $page->save()) {
            if ($page->type == Container::TYPE_MARKDOWN) {
                File::attachPrecreated($page, Yii::$app->request->post('fileUploaderHiddenGuidField'));
            }

            return $this->redirect(['index']);
        }

        return $this->render('@custom_pages/views/common/edit', [
                    'page' => $page,
                    'subNav' => \humhub\modules\custom_pages\widgets\AdminMenu::widget()
        ]);
    }

    public function actionDelete($id)
    {
        $page = $this->findByid($id);

        if ($page !== null) {
            $page->delete();
        }

        return $this->redirect(['index']);
    }

    protected function findAll()
    {
        return Page::find()->all();
    }

    protected function getPageClassName()
    {
        return Page::className();
    }
    
    protected function findById($id)
    {
        return Page::findOne(['id' => $id]);
    }

}
