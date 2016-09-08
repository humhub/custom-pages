<?php

namespace humhub\modules\custom_pages\controllers;

use Yii;
use humhub\modules\custom_pages\models\Snippet;
use humhub\modules\custom_pages\components\TemplateViewBehavior;
use yii\web\HttpException;

/**
 * SnippetController
 *
 * @author buddha
 */
class SnippetController extends AdminController
{   
    
    public $canEdit;
    
    /**
     * @inhritdoc
     */
    public function behaviors()
    {
        $result = parent::behaviors();
        $result[] = ['class' => TemplateViewBehavior::className()];
        return $result;
    }
    
    public function actionEditSnippet()
    {   
        $snippet = $this->findById(Yii::$app->request->get('id'));
        
        if($snippet == null) {
            throw new HttpException(404, 'Snippet not found!');
        }
        
        return $this->render('@custom_pages/views/admin/edit_snippet', [
            'snippet' => $snippet,
            'html' => $this->renderTemplate($snippet, true)
        ]);
        
    }
    
    protected function findAll()
    {
        return Snippet::find()->all();
    }

    protected function getPageClassName()
    {
        return Snippet::className();
    }
    
    protected function findById($id)
    {
        return Snippet::findOne(['id' => $id]);
    }
}
