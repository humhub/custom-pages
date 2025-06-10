<?php

namespace humhub\modules\custom_pages\controllers;

use humhub\modules\custom_pages\models\CustomPage;
use humhub\modules\custom_pages\modules\template\services\TemplateInstanceRendererService;
use humhub\modules\custom_pages\types\HtmlType;
use humhub\modules\custom_pages\types\IframeType;
use humhub\modules\custom_pages\types\LinkType;
use humhub\modules\custom_pages\types\MarkdownType;
use humhub\modules\custom_pages\helpers\PageType;
use humhub\modules\custom_pages\types\PhpType;
use humhub\modules\custom_pages\types\TemplateType;
use Yii;
use yii\helpers\Html;
use yii\web\HttpException;

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
    protected function getAccessRules()
    {
        return [
            ['strict'],
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

        $this->view->setPageTitle(Html::encode($page->title));

        if (!$page->getTargetModel()->isAllowedContentType($page->type)) {
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
        if ($this->contentContainer) {
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

    /**
     * @param CustomPage $page
     * @return string
     * @throws HttpException
     */
    public function renderGlobalView($page)
    {
        switch ($page->type) {
            case HtmlType::ID:
                return $this->render('@custom_pages/views/global/html', ['page' => $page, 'html' => $page->getPageContent(), 'title' => $page->title]);
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
                    'title' => $page->title,
                ]);
            case PhpType::ID:
                return $this->render('@custom_pages/views/global/php', ['page' => $page]);
            default:
                throw new HttpException('500', 'Invalid page type!');
        }
    }

    /**
     * @param CustomPage $page
     * @param $view
     * @return string rendered template page
     * @throws HttpException in case the page is protected from non admin access
     */
    public function viewTemplatePage(CustomPage $page, $view): string
    {
        $mode = Yii::$app->request->get('mode', '');
        $canEdit = $page->content->canEdit();

        if ($mode === 'edit' && !$canEdit) {
            throw new HttpException(403);
        }

        return $this->owner->render($view, [
            'page' => $page,
            'canEdit' => $canEdit,
            'html' => TemplateInstanceRendererService::instance($page, $mode)->render(),
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
    protected function getPageType(): string
    {
        return PageType::Page;
    }
}
