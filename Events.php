<?php

namespace humhub\modules\custom_pages;

use humhub\modules\admin\widgets\AdminMenu;
use humhub\modules\admin\permissions\ManageModules;
use humhub\modules\custom_pages\helpers\Url;
use humhub\modules\custom_pages\models\Page;
use humhub\modules\custom_pages\models\ContainerPage;
use humhub\modules\custom_pages\models\ContainerSnippet;
use humhub\modules\custom_pages\widgets\SnippetWidget;
use humhub\modules\custom_pages\models\Snippet;
use humhub\modules\custom_pages\modules\template\models\PagePermission;
use humhub\modules\custom_pages\permissions\ManagePages;
use humhub\modules\ui\menu\MenuLink;
use humhub\modules\user\widgets\PeopleHeadingButtons;
use Throwable;
use Yii;
use yii\helpers\Html;

/**
 * CustomPagesEvents
 *
 * @author luke
 */
class Events
{

    public static function onBeforeRequest()
    {
        try {
            static::registerAutoloader();
        } catch (Throwable $e) {
            Yii::error($e);
        }
    }

    /**
     * Register composer autoloader
     */
    public static function registerAutoloader()
    {
        Yii::setAlias('@vendor/tinymce/tinymce', '@custom_pages/vendor/tinymce/tinymce');
        Yii::setAlias('@vendor/2amigos/yii2-tinymce-widget/src/assets', '@custom_pages/vendor/2amigos/yii2-tinymce-widget/src/assets');

        require Yii::getAlias('@custom_pages/vendor/autoload.php');
    }

    public static function onAdminMenuInit($event)
    {
        try {
            Yii::$app->moduleManager->getModule('custom_pages')->checkOldGlobalContent();

            if (!Yii::$app->user->can([ManageModules::class, ManagePages::class])) {
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
                'isVisible' => true,
            ]);
        } catch (Throwable $e) {
            Yii::error($e);
        }
    }

    public static function onSpaceMenuInit($event)
    {
        try {
            Yii::$app->moduleManager->getModule('custom_pages')->checkOldGlobalContent();

            /* @var $space \humhub\modules\space\models\Space */
            $space = $event->sender->space;
            if ($space->moduleManager->isEnabled('custom_pages')) {
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
                            && Yii::$app->controller->id === 'view'
                            && Yii::$app->controller->action->id === 'index' && Yii::$app->request->get('id') == $page->id),
                        'sortOrder' => ($page->sort_order != '') ? $page->sort_order : 1000,
                    ]);
                }
            }
        } catch (Throwable $e) {
            Yii::error($e);
        }
    }

    public static function onSpaceAdminMenuInit($event)
    {
        try {
            Yii::$app->moduleManager->getModule('custom_pages')->checkOldGlobalContent();

            /* @var $space \humhub\modules\space\models\Space */
            $space = $event->sender->space;
            if ($space->moduleManager->isEnabled('custom_pages') && $space->isAdmin() && $space->isMember()) {
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
        } catch (Throwable $e) {
            Yii::error($e);
        }
    }

    public static function onTopMenuInit($event)
    {
        try {
            Yii::$app->moduleManager->getModule('custom_pages')->checkOldGlobalContent();

            foreach (Page::findAll(['target' => Page::NAV_CLASS_TOPNAV]) as $page) {

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
        } catch (Throwable $e) {
            Yii::error($e);
        }
    }

    public static function onAccountMenuInit($event)
    {
        try {
            Yii::$app->moduleManager->getModule('custom_pages')->checkOldGlobalContent();

            foreach (Page::findAll(['target' => Page::NAV_CLASS_ACCOUNTNAV]) as $page) {
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
        } catch (Throwable $e) {
            Yii::error($e);
        }
    }

    public static function onAccountTopMenuInit($event)
    {
        if (!Yii::$app->user->isAdmin() &&
            version_compare(Yii::$app->version, '1.8', '<') &&
            !AdminMenu::canAccess()
        ) {
            static::onAdminMenuInit($event);
        }
    }

    public static function onDashboardSidebarInit($event)
    {
        try {
            Yii::$app->moduleManager->getModule('custom_pages')->checkOldGlobalContent();

            $snippets = Snippet::findAll(['target' => Snippet::SIDEBAR_DASHBOARD]);
            $canEdit = PagePermission::canEdit();
            foreach ($snippets as $snippet) {
                if (!$snippet->canView()) {
                    continue;
                }
                $event->sender->addWidget(SnippetWidget::class, ['model' => $snippet, 'canEdit' => $canEdit], ['sortOrder' => $snippet->sort_order]);
            }
        } catch (Throwable $e) {
            Yii::error($e);
        }
    }

    public static function onSpaceSidebarInit($event)
    {
        try {
            Yii::$app->moduleManager->getModule('custom_pages')->checkOldGlobalContent();

            $space = $event->sender->space;
            $canEdit = PagePermission::canEdit();
            if ($space->moduleManager->isEnabled('custom_pages')) {
                $snippets = ContainerSnippet::find()->contentContainer($space)->all();
                foreach ($snippets as $snippet) {
                    if (!$snippet->canView()) {
                        continue;
                    }

                    $event->sender->addWidget(SnippetWidget::class, ['model' => $snippet, 'canEdit' => $canEdit], ['sortOrder' => $snippet->sort_order]);
                }
            }
        } catch (Throwable $e) {
            Yii::error($e);
        }
    }

    public static function onFooterMenuInit($event)
    {
        try {
            foreach (Page::findAll(['target' => Page::NAV_CLASS_FOOTER]) as $page) {
                if (!$page->canView()) {
                    continue;
                }

                $event->sender->addItem([
                    'label' => Html::encode($page->title),
                    'url' => Url::to(['/custom_pages/view', 'id' => $page->id]),
                    'htmlOptions' => ['target' => ($page->in_new_window) ? '_blank' : ''],
                    'sortOrder' => ($page->sort_order != '') ? $page->sort_order : 1000,
                ]);
            }
        } catch (Throwable $e) {
            Yii::error($e);
        }
    }

    public static function onPeopleHeadingButtonsInit($event)
    {
        try {
            /* @var PeopleHeadingButtons $peopleHeadingButtons */
            $peopleHeadingButtons = $event->sender;
            foreach (Page::findAll(['target' => Page::NAV_CLASS_PEOPLE]) as $page) {
                if (!$page->canView()) {
                    continue;
                }

                $peopleHeadingButtons->addEntry(new MenuLink([
                    'label' => Html::encode($page->title),
                    'url' => Url::to(['/custom_pages/view', 'id' => $page->id]),
                    'htmlOptions' => ['target' => ($page->in_new_window) ? '_blank' : ''],
                    'sortOrder' => ($page->sort_order != '') ? $page->sort_order : 1000,
                    'icon' => $page->icon,
                ]));
            }
        } catch (Throwable $e) {
            Yii::error($e);
        }
    }

}
