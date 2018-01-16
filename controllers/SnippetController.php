<?php

namespace humhub\modules\custom_pages\controllers;

use Yii;
use humhub\modules\custom_pages\models\Snippet;
use humhub\modules\custom_pages\components\TemplateViewBehavior;
use yii\web\HttpException;

/**
 * Controller for managing Snippets.
 *
 * @author buddha
 */
class SnippetController extends AdminController
{
    /**
     * @inhritdoc
     */
    public function behaviors()
    {
        $result = parent::behaviors();
        $result[] = ['class' => TemplateViewBehavior::className()];
        return $result;
    }

    public function actionIndex()
    {
        return parent::actionPages();
    }
    
    /**
     * Action for viewing the snippet inline edit view.
     * 
     * @return string
     * @throws HttpException if snippet could not be found.
     */
    public function actionEditSnippet()
    {   
        $snippet = $this->findById(Yii::$app->request->get('id'));
        
        if($snippet == null) {
            throw new HttpException(404, 'Snippet not found!');
        }
        
        return $this->render('edit_snippet', [
            'snippet' => $snippet,
            'html' => $this->renderTemplate($snippet, true)
        ]);
    }
    
    /**
     * @inhritdoc
     */
    protected function findAll()
    {
        return Snippet::find()->all();
    }

    /**
     * @inhritdoc
     */
    protected function getPageClassName()
    {
        return Snippet::className();
    }

    /**
     * @param int $id integer
     * @return Snippet
     */
    protected function findById($id)
    {
        return Snippet::findOne(['id' => $id]);
    }
}
