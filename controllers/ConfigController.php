<?php

namespace humhub\modules\custom_pages\controllers;

use Yii;
use humhub\modules\admin\components\Controller;
use humhub\modules\custom_pages\models\forms\SettingsForm;
use humhub\modules\custom_pages\widgets\AdminMenu;

class ConfigController extends Controller
{
    /**
     * @return string
     * @throws \Exception
     */
    public function actionIndex()
    {
        $model = new SettingsForm();

        if($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->view->saved();
        }

        return $this->render('settings', [
            'model' => $model,
            'subNav' => AdminMenu::widget()
        ]);
    }
}