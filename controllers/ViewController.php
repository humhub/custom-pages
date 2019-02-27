<?php

namespace humhub\modules\custom_pages\controllers;

use humhub\modules\custom_pages\models\ContentType;
use humhub\modules\custom_pages\models\HtmlType;
use humhub\modules\custom_pages\models\IframeType;
use humhub\modules\custom_pages\models\LinkType;
use humhub\modules\custom_pages\models\MarkdownType;
use humhub\modules\custom_pages\models\PageType;
use humhub\modules\custom_pages\models\PhpType;
use humhub\modules\custom_pages\models\TemplateType;
use Yii;
use yii\base\ViewNotFoundException;
use yii\web\HttpException;
use humhub\components\Controller;
use humhub\modules\custom_pages\models\Page;
use humhub\modules\custom_pages\components\Container;
use humhub\modules\custom_pages\components\TemplateViewBehavior;

/**
 * Controller for viewing Pages.
 *
 * @author buddha
 */
class ViewController extends AbstractPageController
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
     * @inhritdoc
     */
    public function behaviors()
    {
        $result = parent::behaviors();
        $result [] = ['class' => TemplateViewBehavior::class];
        return $result;
    }

    /**
     * @param $id
     * @return PageController|string|\yii\console\Response|\yii\web\Response
     * @throws HttpException
     */
    public function actionIndex($id)
    {
        $page = Page::findOne(['id' => $id]);

        if (!$page) {
            throw new HttpException('404', 'Could not find requested page');
        }

        if ($page->admin_only) {
            $this->adminOnly();
        }

        $sub = ($page->getTargetModel()->getSubLayout())
            ? $page->getTargetModel()->getSubLayout()
            : $this->subLayout;

        $this->view->pageTitle = $page->title;

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
                return $this->viewTemplatePage($page);
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
                return $this->viewTemplatePage($page);
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
     * This redirect is needed within some common views shared with container page logic.
     * @return string
     * @throws HttpException
     */
    public function actionView()
    {
        return $this->actionIndex();
    }
}
