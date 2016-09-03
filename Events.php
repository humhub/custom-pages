<?php

namespace humhub\modules\custom_pages;

use Yii;
use yii\helpers\Url;
use humhub\modules\custom_pages\models\Page;
use humhub\modules\custom_pages\components\Container;
use humhub\modules\custom_pages\models\ContainerPage;
use humhub\modules\custom_pages\models\ContainerSnippet;
use humhub\modules\custom_pages\widgets\SnippetWidget;
use humhub\modules\custom_pages\models\Snippet;

/**
 * CustomPagesEvents
 *
 * @author luke
 */
class Events extends \yii\base\Object
{

    public static function onAdminMenuInit($event)
    {
        $event->sender->addItem(array(
            'label' => Yii::t('CustomPagesModule.base', 'Custom Pages'),
            'url' => Url::to(['/custom_pages/admin']),
            'group' => 'manage',
            'icon' => '<i class="fa fa-file-o"></i>',
            'isActive' => (Yii::$app->controller->module && Yii::$app->controller->module->id == 'custom_pages' && Yii::$app->controller->id == 'admin'),
            'sortOrder' => 300,
        ));
    }

    public static function onSpaceMenuInit($event)
    {
        $space = $event->sender->space;
        if ($space->isModuleEnabled('custom_pages')) {
            $pages = ContainerPage::find()->contentContainer($space)->all();
            foreach ($pages as $page) {
                $event->sender->addItem([
                    'label' => \yii\helpers\Html::encode($page->title),
                    'group' => 'modules',
                    'target' => ($page->in_new_window) ? '_blank' : '',
                    'url' => $space->createUrl('/custom_pages/container/view', ['id' => $page->id]),
                    'icon' => '<i class="fa ' . \yii\helpers\Html::encode($page->icon) . '"></i>',
                    'isActive' => (Yii::$app->controller->module && Yii::$app->controller->module->id == 'custom_pages' && Yii::$app->controller->id == 'container' && Yii::$app->controller->action->id == 'view' && Yii::$app->request->get('id') == $page->id),
                    'sortOrder' => ($page->sort_order != '') ? $page->sort_order : 1000,
                ]);
            }
        }
    }

    public static function onSpaceHeaderMenuInit($event)
    {
        if (Yii::$app->controller->module && Yii::$app->controller->module->id == 'custom_pages' && Yii::$app->controller->id == 'container' && Yii::$app->controller->action->id == 'view' && modules\template\models\TemplatePagePermission::canEdit()) {

            $page = ContainerPage::find()->contentContainer(Yii::$app->controller->contentContainer)->where(['custom_pages_container_page.id' => Yii::$app->request->get('id')])->one();

            if ($page->type == Container::TYPE_TEMPLATE) {
                $event->sender->addWidget(modules\template\widgets\TemplatePageEditButton::className(), [], ['sortOrder' => 500]);
            }
        }
    }

    public static function onTopStackInit($event)
    {
        if (Yii::$app->controller->module && Yii::$app->controller->module->id == 'custom_pages' && Yii::$app->controller->id == 'view' && (Yii::$app->controller->action->id == 'view' || Yii::$app->controller->action->id == 'index') && modules\template\models\TemplatePagePermission::canEdit()) {

            $page = Page::findOne(['id' => Yii::$app->request->get('id')]);

            if ($page->type == Container::TYPE_TEMPLATE) {
                $event->sender->addWidget(modules\template\widgets\TemplatePageEditStackMenuButton::className(), [], ['sortOrder' => 500]);
            }
        }
    }

    public static function onSpaceAdminMenuInit($event)
    {
        $space = $event->sender->space;
        if ($space->isModuleEnabled('custom_pages') && $space->isAdmin() && $space->isMember()) {
            $event->sender->addItem(array(
                'label' => Yii::t('CustomPagesModule.base', 'Custom Pages'),
                'group' => 'admin',
                'url' => $space->createUrl('/custom_pages/container/list'),
                'icon' => '<i class="fa fa-file-o"></i>',
                'isActive' => (Yii::$app->controller->module && Yii::$app->controller->module->id == 'custom_pages' && Yii::$app->controller->id == 'container' && Yii::$app->controller->action->id != 'view'),
            ));
        }
    }

    public static function onTopMenuInit($event)
    {
        foreach (Page::findAll(['navigation_class' => Page::NAV_CLASS_TOPNAV]) as $page) {

            // Admin only
            if ($page->admin_only == 1 && !Yii::$app->user->isAdmin()) {
                continue;
            }

            $event->sender->addItem(array(
                'label' => $page->title,
                'url' => Url::to(['/custom_pages/view', 'id' => $page->id]),
                'target' => ($page->in_new_window) ? '_blank' : '',
                'icon' => '<i class="fa ' . $page->icon . '"></i>',
                'isActive' => (Yii::$app->controller->module && Yii::$app->controller->module->id == 'custom_pages' && Yii::$app->controller->id == 'view' && Yii::$app->request->get('id') == $page->id),
                'sortOrder' => ($page->sort_order != '') ? $page->sort_order : 1000,
            ));

            if ($page->type == Container::TYPE_TEMPLATE && !Yii::$app->user->isGuest && Yii::$app->user->getIdentity()->isSystemAdmin()) {
                
            }
        }
    }

    public static function onAccountMenuInit($event)
    {
        foreach (Page::findAll(['navigation_class' => Page::NAV_CLASS_ACCOUNTNAV]) as $page) {
            // Admin only
            if ($page->admin_only == 1 && !Yii::$app->user->isAdmin()) {
                continue;
            }

            $event->sender->addItem(array(
                'label' => $page->title,
                'url' => Url::to(['/custom_pages/view', 'id' => $page->id]),
                'target' => ($page->in_new_window) ? '_blank' : '',
                'icon' => '<i class="fa ' . $page->icon . '"></i>',
                'isActive' => (Yii::$app->controller->module && Yii::$app->controller->module->id == 'custom_pages' && Yii::$app->controller->id == 'view' && Yii::$app->request->get('id') == $page->id),
                'sortOrder' => ($page->sort_order != '') ? $page->sort_order : 1000,
            ));
        }
    }

    public static function onDashboardSidebarInit($event)
    {
        if (Yii::$app->user->isGuest) {
            return;
        }

        $snippets = Snippet::find(['sidebar' => Snippet::SIDEBAR_DASHBOARD])->all();
        foreach ($snippets as $snippet) {
            $event->sender->addWidget(SnippetWidget::className(), ['model' => $snippet], ['sortOrder' => $snippet->sort_order]);
        }
    }

    public static function onSpaceSidebarInit($event)
    {
        if (Yii::$app->user->isGuest) {
            return;
        }

        $space = $event->sender->space;
        if ($space->isModuleEnabled('custom_pages')) {
            $snippets = ContainerSnippet::find()->contentContainer($space)->all();
            foreach ($snippets as $snippet) {
                $event->sender->addWidget(SnippetWidget::className(), ['model' => $snippet], ['sortOrder' => $snippet->sort_order]);
            }
        }
    }

}
