<?php

namespace humhub\modules\custom_pages\modules\template\controllers;

use Yii;
use humhub\components\Controller;

/**
 * UploadController
 *
 * @author buddha
 */
class UploadController extends Controller
{

    public function behaviors()
    {
        return [
            [
                'class' => 'humhub\modules\custom_pages\modules\template\components\TemplateAccessFilter'
            ],
        ];
    }
    
    public function actionUploadFile()
    {
        $result = Yii::$app->runAction('/file/file/upload');

        Yii::$app->response->format = 'json';
        
        return [
            'success' => true,
            'url' => $result['files'][0]['url'],
            'name' => $result['files'][0]['name'],
            'message' => $result['files'][0]['error'],
            'guid' => $result['files'][0]['guid'],  
        ];
        
    }

    public function actionUploadCkeditorFile()
    {
        $funcNum = Yii::$app->request->get('CKEditorFuncNum');
        // Optional: instance name (might be used to load a specific configuration file or anything else).
        $CKEditor = Yii::$app->request->get('CKEditor');
        // Optional: might be used to provide localized messages.
        $langCode = Yii::$app->request->get('langCode');
        /** Optional: compare it with the value of `ckCsrfToken` sent in a cookie to protect your server side uploader against CSRF.
          $token = $_POST['ckCsrfToken']; * */
        $_FILES['files'] = $_FILES['upload'];

        $result = Yii::$app->runAction('/file/file/upload');

        Yii::$app->response->format = \yii\web\Response::FORMAT_HTML;
        
        $url = $result['files'][0]['url'];
        $message = $result['files'][0]['error'];
        $guid = $result['files'][0]['guid'];

        $script = "window.parent.CKEDITOR.tools.callFunction($funcNum, '$url', '$message');";
        $script .= "window.parent.ckeditorAddUploadedFile('$guid');";
        return \yii\helpers\Html::tag('script', $script, ['type' => 'text/javascript']);
    }

}
