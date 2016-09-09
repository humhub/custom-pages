<?php

namespace humhub\modules\custom_pages\modules\template\controllers;

use Yii;
use humhub\components\Controller;

/**
 * Upload controller for ckeditor file uploads
 *
 * @author buddha
 */
class UploadController extends Controller
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => 'humhub\modules\custom_pages\modules\template\components\TemplateAccessFilter'
            ],
        ];
    }

    /**
     * Used as adapter action for ckeditor file 
     * @return type
     */
    public function actionUploadCkeditorFile($CKEditorFuncNum)
    {
        $_FILES['files'] = $_FILES['upload'];

        $result = Yii::$app->runAction('/file/file/upload');

        Yii::$app->response->format = \yii\web\Response::FORMAT_HTML;
        
        $url = $result['files'][0]['url'];
        $message = $result['files'][0]['error'];
        $guid = $result['files'][0]['guid'];

        $script = "window.parent.CKEDITOR.tools.callFunction($CKEditorFuncNum, '$url', '$message');";
        $script .= "window.parent.ckeditorAddUploadedFile('$guid');";
        return \yii\helpers\Html::tag('script', $script, ['type' => 'text/javascript']);
    }

}
