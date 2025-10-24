<?php

namespace humhub\modules\custom_pages\controllers;

use humhub\modules\custom_pages\helpers\PageType;
use humhub\modules\custom_pages\models\CustomPage;
use humhub\modules\custom_pages\modules\template\services\TemplateInstanceRendererService;
use humhub\modules\custom_pages\permissions\ManagePages;
use humhub\modules\custom_pages\types\HtmlType;
use humhub\modules\custom_pages\types\IframeType;
use humhub\modules\custom_pages\types\LinkType;
use humhub\modules\custom_pages\types\MarkdownType;
use humhub\modules\custom_pages\types\PhpType;
use humhub\modules\custom_pages\types\TemplateType;
use Yii;
use yii\helpers\Html;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

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
        $page = CustomPage::findOne(Yii::$app->request->get('id'));

        if (!$page || !$page->getTargetModel()->isAllowedContentType($page->type)) {
            throw new NotFoundHttpException('Could not find the requested page');
        }

        if (!Yii::$app->user->can([ManagePages::class]) && !$page->canView()) {
            throw new ForbiddenHttpException('Cannot view the requested page');
        }

        $this->subLayout = $page->getTargetModel()->getSubLayout() ?: $this->subLayout;

        $this->view->setPageTitle(Html::encode($page->title));

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
        return match ($page->type) {
            HtmlType::ID => $this->render('@custom_pages/views/container/html', ['page' => $page, 'html' => $page->getPageContent(), 'title' => $page->title]),
            IframeType::ID => $this->render('@custom_pages/views/container/iframe', ['page' => $page, 'url' => $page->page_content]),
            TemplateType::ID => $this->viewTemplatePage($page, '@custom_pages/views/container/template'),
            LinkType::ID => $this->redirect($page->page_content),
            MarkdownType::ID => $this->render('@custom_pages/views/container/markdown', ['page' => $page, 'md' => $page->page_content]),
            PhpType::ID => $this->render('@custom_pages/views/container/php', ['page' => $page, 'contentContainer' => $this->contentContainer]),
            default => throw new HttpException('500', 'Invalid page type!'),
        };
    }

    /**
     * @param CustomPage $page
     * @return string
     * @throws HttpException
     */
    public function renderGlobalView($page)
    {
        return match ($page->type) {
            HtmlType::ID => $this->render('@custom_pages/views/global/html', ['page' => $page, 'html' => $page->getPageContent(), 'title' => $page->title]),
            IframeType::ID => $this->render('@custom_pages/views/global/iframe', ['page' => $page, 'url' => $page->page_content, 'navigationClass' => $page->getTargetId()]),
            TemplateType::ID => $this->viewTemplatePage($page, '@custom_pages/views/global/template'),
            LinkType::ID => $this->redirect($page->page_content),
            MarkdownType::ID => $this->render('@custom_pages/views/global/markdown', [
                'page' => $page,
                'md' => $page->page_content,
                'navigationClass' => $page->getTargetId(),
                'title' => $page->title,
            ]),
            PhpType::ID => $this->render('@custom_pages/views/global/php', ['page' => $page]),
            default => throw new HttpException('500', 'Invalid page type!'),
        };
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

        if ($mode === 'edit' && !$page->content->canEdit() && !$page->isEditor()) {
            throw new ForbiddenHttpException('Access denied!');
        }

        return $this->owner->render($view, [
            'page' => $page,
            'html' => TemplateInstanceRendererService::instance($page, $mode === 'edit')->render(),
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
