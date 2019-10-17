<?php

namespace humhub\modules\custom_pages\controllers;

use Yii;
use humhub\modules\custom_pages\models\ContainerSnippet;
use humhub\modules\custom_pages\models\PageType;
use humhub\modules\custom_pages\models\Snippet;
use yii\web\HttpException;

/**
 * Controller for managing Snippets.
 *
 * @author buddha
 */
class SnippetController extends PageController
{
    /**
     * Action for viewing the snippet inline edit view.
     * 
     * @return string
     * @throws HttpException if snippet could not be found.
     */
    public function actionEditSnippet($id)
    {   
        $snippet = $this->findById(['id' => $id]);
        
        if(!$snippet) {
            throw new HttpException(404, 'Snippet not found!');
        }

        $view = $this->contentContainer
            ? '@custom_pages/views/container/edit_snippet'
            : '@custom_pages/views/global/edit_snippet';
        
        return $this->render($view, [
            'snippet' => $snippet,
            'contentContainer' => $this->contentContainer,
            'html' => $this->renderTemplate($snippet, true)
        ]);
    }

    protected function getPageClassName()
    {
        return $this->contentContainer ? ContainerSnippet::class : Snippet::class;
    }

    protected function getPageType()
    {
        return PageType::Snippet;
    }
}
