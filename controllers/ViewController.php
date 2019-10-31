<?php

namespace humhub\modules\custom_pages\controllers;

use Yii;
use yii\helpers\Html;
use yii\web\HttpException;
use humhub\modules\custom_pages\models\CustomContentContainer;
use humhub\modules\custom_pages\models\HtmlType;
use humhub\modules\custom_pages\models\IframeType;
use humhub\modules\custom_pages\models\LinkType;
use humhub\modules\custom_pages\models\MarkdownType;
use humhub\modules\custom_pages\models\PageType;
use humhub\modules\custom_pages\models\PhpType;
use humhub\modules\custom_pages\models\TemplateType;
use humhub\modules\custom_pages\modules\template\components\TemplateRenderer;


/**
 * Controller for viewing Pages.
 *
 * @author buddha
 */
class ViewController extends AbstractCustomContainerController
{

    /**
     * @inhritdoc
     */
    public function getAccessRules()
    {
        return [
            ['strict']
        ];
    }

    /**
     * @param $id
     * @return PageController|string|\yii\console\Response|\yii\web\Response
     * @throws HttpException
     * @throws \Throwable
     * @throws \yii\base\Exception
     */
    public function actionIndex()
    {
        $page = $this->findById(Yii::$app->request->get('id'));

        if (!$page) {
            throw new HttpException('404', 'Could not find requested page');
        }

        if (!$page->canView()) {
            throw new HttpException(403);
        }

        $this->subLayout = ($page->getTargetModel()->getSubLayout())
            ? $page->getTargetModel()->getSubLayout()
            : $this->subLayout;

        $this->view->pageTitle = Html::encode($page->title);

        if(!$page->getTargetModel()->isAllowedContentType($page->type)) {
            throw new HttpException(404);
        }

        return $this->renderView($page);


    }

    /**
     * @param $page
     * @return PageController|string|\yii\console\Response|\yii\web\Response
     * @throws HttpException
     */
    public function renderView($page)
    {
        if($this->contentContainer) {
            return $this->renderContainerView($page);
        }

        return $this->renderGlobalView($page);
    }


    public function renderContainerView($page)
    {
        switch ($page->type) {
            case IframeType::ID:
                return $this->render('@custom_pages/views/container/iframe', ['page' => $page, 'url' => $page->page_content]);
            case TemplateType::ID:
                return $this->viewTemplatePage($page, '@custom_pages/views/container/template');
            case LinkType::ID:
                return $this->redirect($page->page_content);
            case MarkdownType::ID:
                return $this->render('@custom_pages/views/container/markdown', ['page' => $page, 'md' => $page->page_content]);
            case PhpType::ID:
                return $this->render('@custom_pages/views/container/php', ['page' => $page, 'contentContainer' => $this->contentContainer]);
            default:
                throw new HttpException('500', 'Invalid page type!');
        }
    }

    public function renderGlobalView($page)
    {
        switch ($page->type) {
            case HtmlType::ID:
                return $this->render('@custom_pages/views/global/html', ['page' => $page, 'html' => $page->page_content, 'title' => $page->title]);
            case IframeType::ID:
                return $this->render('@custom_pages/views/global/iframe', ['page' => $page, 'url' => $page->page_content, 'navigationClass' => $page->getTargetId()]);
            case TemplateType::ID:
                return $this->viewTemplatePage($page, '@custom_pages/views/global/template');
            case LinkType::ID:
                return $this->redirect($page->page_content);
            case MarkdownType::ID:
                return $this->render('@custom_pages/views/global/markdown', [
                    'page' => $page,
                    'md' => $page->page_content,
                    'navigationClass' => $page->getTargetId(),
                    'title' => $page->title
                ]);
            case PhpType::ID:
                return $this->render('@custom_pages/views/global/php', ['page' => $page]);
            default:
                throw new HttpException('500', 'Invalid page type!');
        }
    }

    /**
     * @param CustomContentContainer $page
     * @param $view
     * @return string rendered template page
     * @throws HttpException in case the page is protected from non admin access
     */
    public function viewTemplatePage(CustomContentContainer $page, $view)
    {
        $editMode = Yii::$app->request->get('editMode');
        $canEdit = $page->content->canEdit();

        if($editMode && !$canEdit) {
            throw new HttpException(403);
        }

        $html = TemplateRenderer::render($page, $editMode);

        return $this->owner->render($view, [
            'page' => $page,
            'editMode' => $editMode,
            'canEdit' => $canEdit,
            'html' => $html
        ]);
    }

    /**
     * This redirect is needed within some common views shared with container page logic.
     * @return string
     * @throws HttpException
     * @throws \Throwable
     * @throws \yii\base\Exception
     */
    public function actionView()
    {
        return $this->actionIndex();
    }

    /**
     * @inheritdoc
     */
    protected function getPageType()
    {
        return PageType::Page;
    }
}
