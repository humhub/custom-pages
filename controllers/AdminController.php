<?php

namespace humhub\modules\custom_pages\controllers;

use Yii;
use humhub\modules\custom_pages\models\CustomPage;

/**
 * AdminController
 *
 * @author luke
 */
class AdminController extends \humhub\modules\admin\components\Controller
{

    public function actionIndex()
    {
        $pages = CustomPage::find()->all();
        return $this->render('index', array('pages' => $pages));
    }

    public function actionEdit()
    {
        $page = CustomPage::findOne(['id' => Yii::$app->request->get('id')]);

        if ($page === null) {
            $page = new CustomPage;
        }

        if ($page->load(Yii::$app->request->post()) && $page->validate() && $page->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('edit', array('page' => $page));
    }

    public function actionDelete()
    {
        $page = CustomPage::findOne(['id' => Yii::$app->request->get('id')]);

        if ($page !== null) {
            $page->delete();
        }

        return $this->redirect(['index']);
    }

}
