<?php

namespace humhub\modules\custom_pages\controllers;

use Yii;
use humhub\modules\admin\components\Controller;
use humhub\modules\custom_pages\models\forms\SettingsForm;

class ConfigController extends Controller
{
    /**
     * @inheritdoc
     */
    public $subLayout = '@custom_pages/views/layouts/admin';

    /**
     * @return string
     * @throws \Exception
     */
    public function actionIndex()
    {
        $model = new SettingsForm();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->view->saved();
        }

        return $this->render('settings', ['model' => $model]);
    }
}
