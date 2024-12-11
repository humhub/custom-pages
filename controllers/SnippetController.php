<?php

namespace humhub\modules\custom_pages\controllers;

use humhub\modules\custom_pages\helpers\PageType;
use humhub\modules\custom_pages\modules\template\services\TemplateInstanceRendererService;
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

        if (!$snippet) {
            throw new HttpException(404, 'Snippet not found!');
        }

        $view = $this->contentContainer
            ? '@custom_pages/views/container/edit_snippet'
            : '@custom_pages/views/global/edit_snippet';

        return $this->render($view, [
            'snippet' => $snippet,
            'contentContainer' => $this->contentContainer,
            'html' => TemplateInstanceRendererService::instance($snippet)->render(true),
        ]);
    }

    protected function getPageType(): string
    {
        return PageType::Snippet;
    }
}
