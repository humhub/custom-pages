<?php

namespace humhub\modules\custom_pages\controllers;

use Yii;
use humhub\modules\custom_pages\models\ContainerSnippet;
use yii\web\HttpException;

/**
 * Custom Pages for ContentContainer
 *
 * @author buddha
 */
class ContainerSnippetController extends ContainerController
{   
    public function actionEditSnippet()
    {
        $this->adminOnly();
        
        $containerSnippet = ContainerSnippet::findOne(['id' => Yii::$app->request->get('id')]);
        
        if($containerSnippet == null) {
            throw new HttpException(404, 'Snippet not found!');
        }
        
        return $this->render('@custom_pages/views/container/edit_snippet', [
            'snippet' => $containerSnippet,
            'contentContainer' => $this->contentContainer,
            'html' => $this->renderTemplate($containerSnippet, true)
        ]);
        
    }
    
    protected function findAll()
    {
        return ContainerSnippet::find()->contentContainer($this->contentContainer)->all();
    }
    
    protected function getPageClassName()
    {
        return ContainerSnippet::className();
    }
    
    protected function findPageById($id = null) 
    {
        return ContainerSnippet::find()->contentContainer($this->contentContainer)->where(['custom_pages_container_snippet.id' => $id])->one();
    }
}
