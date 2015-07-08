<?php

namespace module\custom_pages;

use Yii;
use yii\helpers\Url;
use module\custom_pages\models\CustomPage;

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

        // Check for Admin Menu Pages to insert
    }

    public static function onTopMenuInit($event)
    {
        foreach (CustomPage::findAll(['navigation_class' => CustomPage::NAV_CLASS_TOPNAV]) as $page) {

            // Admin only
            if ($page->admin_only == 1 && !Yii::$app->user->isAdmin()) {
                continue;
            }

            $event->sender->addItem(array(
                'label' => $page->title,
                'url' => Url::to(['/custom_pages/view', 'id' => $page->id]),
                'target' => ($page->type == CustomPage::TYPE_LINK) ? '_blank' : '',
                'icon' => '<i class="fa ' . $page->icon . '"></i>',
                'isActive' => (Yii::$app->controller->module && Yii::$app->controller->module->id == 'custom_pages' && Yii::$app->controller->id == 'view' && Yii::$app->request->get('id') == $page->id),
                'sortOrder' => ($page->sort_order != '') ? $page->sort_order : 1000,
            ));
        }
    }

    public static function onAccountMenuInit($event)
    {
        foreach (CustomPage::findAll(['navigation_class' => CustomPage::NAV_CLASS_ACCOUNTNAV]) as $page) {
            // Admin only
            if ($page->admin_only == 1 && !Yii::$app->user->isAdmin()) {
                continue;
            }

            $event->sender->addItem(array(
                'label' => $page->title,
                'url' => Url::to(['/custom_pages/view', 'id' => $page->id]),
                'target' => ($page->type == CustomPage::TYPE_LINK) ? '_blank' : '',
                'icon' => '<i class="fa ' . $page->icon . '"></i>',
                'isActive' => (Yii::$app->controller->module && Yii::$app->controller->module->id == 'custom_pages' && Yii::$app->controller->id == 'view' && Yii::$app->request->get('id') == $page->id),
                'sortOrder' => ($page->sort_order != '') ? $page->sort_order : 1000,
            ));
        }
    }

}
