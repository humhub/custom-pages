<?php

namespace humhub\modules\custom_pages;

use humhub\modules\admin\permissions\ManageModules;
use humhub\modules\admin\widgets\AdminMenu;
use humhub\modules\content\helpers\ContentContainerHelper;
use humhub\modules\custom_pages\helpers\Url;
use humhub\modules\custom_pages\interfaces\CustomPagesService;
use humhub\modules\custom_pages\modules\template\helpers\PagePermissionHelper;
use humhub\modules\custom_pages\types\LinkType;
use humhub\modules\custom_pages\models\CustomPage;
use humhub\modules\custom_pages\helpers\PageType;
use humhub\modules\custom_pages\permissions\ManagePages;
use humhub\modules\custom_pages\widgets\SnippetWidget;
use humhub\modules\space\models\Space;
use humhub\modules\ui\menu\MenuLink;
use humhub\modules\user\widgets\PeopleHeadingButtons;
use humhub\widgets\TopMenu;
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
                    && (Yii::$app->controller->module->id === 'custom_pages'
                        && (Yii::$app->controller->id === 'page' || Yii::$app->controller->id === 'config'))
                    ||  Yii::$app->controller->module->id == 'template'),
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

            /* @var $space Space */
            $space = $event->sender->space;
            if ($space->moduleManager->isEnabled('custom_pages')) {
                foreach (CustomPagesService::instance()->findByTarget(PageType::TARGET_SPACE_MENU, $space)->all() as $page) {
                    /* @var CustomPage $page */
                    if (!$page->canView()) {
                        continue;
                    }

                    $event->sender->addItem([
                        'label' => Html::encode(Yii::t('CustomPagesModule.base', $page->title)),
                        'group' => 'modules',
                        'htmlOptions' => [
                            'target' => ($page->in_new_window) ? '_blank' : '',
                            'data-pjax-prevent' => 1,
                        ],
                        'url' => $page->getUrl(),
                        'icon' => '<i class="fa ' . Html::encode($page->icon) . '"></i>',
                        'isActive' => (Yii::$app->controller->module
                            && Yii::$app->controller->module->id === 'custom_pages'
                            && Yii::$app->controller->id === 'view'
                            && Yii::$app->controller->action->id === 'index' && Yii::$app->request->get('id') == $page->id),
                        'sortOrder' => $page->sort_order ?: 1000 + $page->id,
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

            /* @var $space Space */
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
        /** @var TopMenu $menu */
        $menu = $event->sender;

        try {
            Yii::$app->moduleManager->getModule('custom_pages')->checkOldGlobalContent();

            foreach (CustomPagesService::instance()->findByTarget(PageType::TARGET_TOP_MENU)->all() as $page) {
                /* @var CustomPage $page */
                if (!$page->canView()) {
                    continue;
                }

                $menu->addEntry(new MenuLink([
                    'id' => 'custom-page-' . $page->id,
                    'label' => Html::encode(Yii::t('CustomPagesModule.base', $page->title)),
                    'url' => ['/custom_pages/view', 'id' => $page->id],
                    'htmlOptions' => ['target' => ($page->in_new_window) ? '_blank' : ''],
                    'icon' => $page->icon,
                    'isActive' => (
                        (
                            MenuLink::isActiveState('custom_pages', 'view')
                            && !Yii::$app->controller->contentContainer
                            && (int)Yii::$app->request->get('id') === $page->id
                        )
                        || static::isCurrentTargetUrl($page)
                    ),
                    'sortOrder' => $page->sort_order ?: 1000 + $page->id,
                ]));
            }
        } catch (Throwable $e) {
            Yii::error($e);
        }
    }

    private static function isCurrentTargetUrl(CustomPage $page): bool
    {
        if ($page->type === LinkType::ID && $page->page_content) {
            $targetUrl = strpos($page->page_content, 'http') === 0 ?
                $page->page_content :
                'https://domain.tld/' . trim($page->page_content, '/');
            $targetUrlPath = parse_url($targetUrl, PHP_URL_PATH) ?: '';
            $targetUrlQuery = parse_url($targetUrl, PHP_URL_QUERY) ?: '';
            $container = ContentContainerHelper::getCurrent();
            $currentContainerPath = $container ?
                rtrim($container->getUrl(), '/') :
                null;
            if (
                $targetUrlPath
                && (
                    $currentContainerPath === $targetUrlPath
                    || Url::to() === $targetUrlPath . ($targetUrlQuery ? '?' . $targetUrlQuery : '')
                )
            ) {
                return true;
            }
        }
        return false;
    }

    public static function onAccountMenuInit($event)
    {
        try {
            Yii::$app->moduleManager->getModule('custom_pages')->checkOldGlobalContent();

            foreach (CustomPagesService::instance()->findByTarget(PageType::TARGET_ACCOUNT_MENU)->all() as $page) {
                /* @var CustomPage $page */
                if (!$page->canView()) {
                    continue;
                }

                $event->sender->addItem([
                    'label' => Html::encode(Yii::t('CustomPagesModule.base', $page->title)),
                    'url' => Url::to(['/custom_pages/view', 'id' => $page->id]),
                    'htmlOptions' => ['target' => ($page->in_new_window) ? '_blank' : ''],
                    'icon' => '<i class="fa ' . Html::encode($page->icon) . '"></i>',
                    'isActive' => (Yii::$app->controller->module
                        && Yii::$app->controller->module->id === 'custom_pages'
                        && Yii::$app->controller->id === 'view' && Yii::$app->request->get('id') == $page->id),
                    'sortOrder' => $page->sort_order ?: 1000 + $page->id,
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
            $canEdit = PagePermissionHelper::canEdit();
            foreach (CustomPagesService::instance()->findByTarget(PageType::TARGET_DASHBOARD_SIDEBAR)->all() as $page) {
                /* @var CustomPage $page */
                if ($page->canView()) {
                    $event->sender->addWidget(SnippetWidget::class, ['model' => $page, 'canEdit' => $canEdit], [
                        'sortOrder' => $page->sort_order ?: 1000 + $page->id,
                    ]);
                }
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
            $canEdit = PagePermissionHelper::canEdit();
            if ($space->moduleManager->isEnabled('custom_pages')) {
                foreach (CustomPagesService::instance()->findByTarget(PageType::TARGET_SPACE_STREAM_SIDEBAR, $space)->all() as $page) {
                    /* @var CustomPage $page */
                    if ($page->canView()) {
                        $event->sender->addWidget(SnippetWidget::class, ['model' => $page, 'canEdit' => $canEdit], [
                            'sortOrder' => $page->sort_order ?: 1000 + $page->id,
                        ]);
                    }
                }
            }
        } catch (Throwable $e) {
            Yii::error($e);
        }
    }

    public static function onFooterMenuInit($event)
    {
        try {
            foreach (CustomPagesService::instance()->findByTarget(PageType::TARGET_FOOTER)->all() as $page) {
                if (!$page->canView()) {
                    continue;
                }

                $event->sender->addItem([
                    'label' => Html::encode(Yii::t('CustomPagesModule.base', $page->title)),
                    'url' => Url::to(['/custom_pages/view', 'id' => $page->id], true),
                    'htmlOptions' => ['target' => ($page->in_new_window) ? '_blank' : ''],
                    'sortOrder' => $page->sort_order ?: 1000 + $page->id,
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
            foreach (CustomPagesService::instance()->findByTarget(PageType::TARGET_PEOPLE)->all() as $page) {
                if (!$page->canView()) {
                    continue;
                }

                $peopleHeadingButtons->addEntry(new MenuLink([
                    'label' => Html::encode(Yii::t('CustomPagesModule.base', $page->title)),
                    'url' => Url::to(['/custom_pages/view', 'id' => $page->id]),
                    'htmlOptions' => ['target' => ($page->in_new_window) ? '_blank' : ''],
                    'sortOrder' => $page->sort_order ?: 1000 + $page->id,
                    'icon' => $page->icon,
                ]));
            }
        } catch (Throwable $e) {
            Yii::error($e);
        }
    }
}
