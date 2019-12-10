<?php

namespace humhub\modules\custom_pages;

use humhub\modules\custom_pages\models\TemplateType;
use Yii;
use yii\helpers\Html;
use humhub\modules\custom_pages\helpers\Url;
use humhub\modules\custom_pages\models\Page;
use humhub\modules\custom_pages\models\ContainerPage;
use humhub\modules\custom_pages\models\ContainerSnippet;
use humhub\modules\custom_pages\widgets\SnippetWidget;
use humhub\modules\custom_pages\models\Snippet;
use humhub\modules\custom_pages\modules\template\models\PagePermission;

/**
 * CustomPagesEvents
 *
 * @author luke
 */
class Events
{

    public static function onAdminMenuInit($event)
    {
        Yii::$app->moduleManager->getModule('custom_pages')->checkOldGlobalContent();

        if (!Yii::$app->user->isAdmin()) {
            return;
        }

        $event->sender->addItem([
            'label' => Yii::t('CustomPagesModule.base', 'Custom Pages'),
            'url' => Url::toPageOverview(),
            'group' => 'manage',
            'icon' => '<i class="fa fa-file-text-o"></i>',
            'isActive' => (Yii::$app->controller->module
                && Yii::$app->controller->module->id === 'custom_pages'
                && (Yii::$app->controller->id === 'page' || Yii::$app->controller->id === 'config')),
            'sortOrder' => 300,
        ]);
    }

    public static function onSpaceMenuInit($event)
    {
        Yii::$app->moduleManager->getModule('custom_pages')->checkOldGlobalContent();

        /* @var $space \humhub\modules\space\models\Space */
        $space = $event->sender->space;
        if ($space->isModuleEnabled('custom_pages')) {
            $pages = ContainerPage::find()->contentContainer($space)->andWhere(['target' => ContainerPage::NAV_CLASS_SPACE_NAV])->all();
            foreach ($pages as $page) {
                if (!$page->canView()) {
                    continue;
                }

                $event->sender->addItem([
                    'label' => Html::encode($page->title),
                    'group' => 'modules',
                    'htmlOptions' => [
                        'target' => ($page->in_new_window) ? '_blank' : '',
                        'data-pjax-prevent' => 1
                    ],
                    'url' => $page->getUrl(),
                    'icon' => '<i class="fa ' . Html::encode($page->icon) . '"></i>',
                    'isActive' => (Yii::$app->controller->module
                        && Yii::$app->controller->module->id === 'custom_pages'
                        && Yii::$app->controller->id === 'container'
                        && Yii::$app->controller->action->id === 'view' && Yii::$app->request->get('id') == $page->id),
                    'sortOrder' => ($page->sort_order != '') ? $page->sort_order : 1000,
                ]);
            }
        }
    }

    public static function onSpaceHeaderMenuInit($event)
    {
        Yii::$app->moduleManager->getModule('custom_pages')->checkOldGlobalContent();

        if (Yii::$app->controller->module
            && Yii::$app->controller->module->id === 'custom_pages'
            && Yii::$app->controller->id === 'container'
            && Yii::$app->controller->action->id === 'view'
            && PagePermission::canEdit()) {

            $page = ContainerPage::find()->contentContainer(Yii::$app->controller->contentContainer)->where(['custom_pages_container_page.id' => Yii::$app->request->get('id')])->one();

            if (TemplateType::isType($page->type)) {
                $event->sender->addWidget(modules\template\widgets\TemplatePageEditButton::class, [], ['sortOrder' => 500]);
            } else {
                $event->sender->addWidget(modules\template\widgets\PageConfigurationButton::class, [], ['sortOrder' => 500]);
            }
        }
    }

    public static function onSpaceAdminMenuInit($event)
    {
        Yii::$app->moduleManager->getModule('custom_pages')->checkOldGlobalContent();

        /* @var $space \humhub\modules\space\models\Space */
        $space = $event->sender->space;
        if ($space->isModuleEnabled('custom_pages') && $space->isAdmin() && $space->isMember()) {
            $event->sender->addItem([
                'label' => Yii::t('CustomPagesModule.base', 'Custom Pages'),
                'group' => 'admin',
                'url' => Url::toPageOverview($space),
                'icon' => '<i class="fa fa-file-text-o"></i>',
                'isActive' => (Yii::$app->controller->module
                    && Yii::$app->controller->module->id === 'custom_pages'
                    && Yii::$app->controller->id === 'container'
                    && Yii::$app->controller->action->id !== 'view'),
            ]);
        }
    }

    public static function onDirectoryMenuInit($event)
    {
        Yii::$app->moduleManager->getModule('custom_pages')->checkOldGlobalContent();

        foreach (Page::findAll(['target' => Page::NAV_CLASS_DIRECTORY]) as $page) {
            // Admin only
            if ($page->admin_only == 1 && !Yii::$app->user->isAdmin()) {
                continue;
            }

            $event->sender->addItem([
                'label' => Html::encode($page->title),
                'url' => Url::to(['/custom_pages/view', 'id' => $page->id]),
                'group' => 'directory',
                'htmlOptions' => ['target' => ($page->in_new_window) ? '_blank' : ''],
                'icon' => '<i class="fa ' . Html::encode($page->icon) . '"></i>',
                'isActive' => (Yii::$app->controller->module
                    && Yii::$app->controller->module->id === 'custom_pages'
                    && Yii::$app->controller->id === 'view' && Yii::$app->request->get('id') == $page->id),
                'sortOrder' => ($page->sort_order != '') ? $page->sort_order : 1000,
            ]);
        }
    }

    public static function onTopMenuInit($event)
    {
        Yii::$app->moduleManager->getModule('custom_pages')->checkOldGlobalContent();

        foreach (Page::findAll(['target' => Page::NAV_CLASS_TOPNAV]) as $page) {

            if (!$page->canView()) {
                continue;
            }

            $event->sender->addItem(array(
                'label' => Html::encode($page->title),
                'url' => Url::to(['/custom_pages/view', 'id' => $page->id]),
                'htmlOptions' => ['target' => ($page->in_new_window) ? '_blank' : ''],
                'icon' => '<i class="fa ' . Html::encode($page->icon) . '"></i>',
                'isActive' => (Yii::$app->controller->module
                    && Yii::$app->controller->module->id === 'custom_pages'
                    && Yii::$app->controller->id === 'view' && Yii::$app->request->get('id') == $page->id),
                'sortOrder' => ($page->sort_order != '') ? $page->sort_order : 1000,
            ));
        }
    }

    public static function onAccountMenuInit($event)
    {
        Yii::$app->moduleManager->getModule('custom_pages')->checkOldGlobalContent();

        foreach (Page::findAll(['target' => Page::NAV_CLASS_ACCOUNTNAV]) as $page) {
            // Admin only
            if (!$page->canView()) {
                continue;
            }

            $event->sender->addItem([
                'label' => Html::encode($page->title),
                'url' => Url::to(['/custom_pages/view', 'id' => $page->id]),
                'htmlOptions' => ['target' => ($page->in_new_window) ? '_blank' : ''],
                'icon' => '<i class="fa ' . Html::encode($page->icon) . '"></i>',
                'isActive' => (Yii::$app->controller->module
                    && Yii::$app->controller->module->id === 'custom_pages'
                    && Yii::$app->controller->id === 'view' && Yii::$app->request->get('id') == $page->id),
                'sortOrder' => ($page->sort_order != '') ? $page->sort_order : 1000,
            ]);
        }
    }

    public static function onDashboardSidebarInit($event)
    {
        Yii::$app->moduleManager->getModule('custom_pages')->checkOldGlobalContent();

        if (Yii::$app->user->isGuest) {
            return;
        }

        $snippets = Snippet::findAll(['target' => Snippet::SIDEBAR_DASHBOARD]);
        $canEdit = PagePermission::canEdit();
        foreach ($snippets as $snippet) {
            if (!$snippet->canView()) {
                continue;
            }
            $event->sender->addWidget(SnippetWidget::class, ['model' => $snippet, 'canEdit' => $canEdit], ['sortOrder' => $snippet->sort_order]);
        }
    }

    public static function onDirectorySidebarInit($event)
    {
        Yii::$app->moduleManager->getModule('custom_pages')->checkOldGlobalContent();

        if (Yii::$app->user->isGuest) {
            return;
        }

        $snippets = Snippet::findAll(['target' => Snippet::SIDEBAR_DIRECTORY]);
        $canEdit = PagePermission::canEdit();
        foreach ($snippets as $snippet) {
            if (!$snippet->canView()) {
                continue;
            }
            $event->sender->addWidget(SnippetWidget::class, ['model' => $snippet, 'canEdit' => $canEdit], ['sortOrder' => $snippet->sort_order]);
        }
    }

    public static function onSpaceSidebarInit($event)
    {
        Yii::$app->moduleManager->getModule('custom_pages')->checkOldGlobalContent();

        if (Yii::$app->user->isGuest) {
            return;
        }

        $space = $event->sender->space;
        $canEdit = PagePermission::canEdit();
        if ($space->isModuleEnabled('custom_pages')) {
            $snippets = ContainerSnippet::find()->contentContainer($space)->all();
            foreach ($snippets as $snippet) {
                if (!$snippet->canView()) {
                    continue;
                }

                $event->sender->addWidget(SnippetWidget::class, ['model' => $snippet, 'canEdit' => $canEdit], ['sortOrder' => $snippet->sort_order]);
            }
        }
    }

}
