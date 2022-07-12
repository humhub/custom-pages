<?php

use humhub\components\Application;
use humhub\modules\custom_pages\Events;
use humhub\modules\dashboard\widgets\Sidebar as DashboardSidebar;
use humhub\modules\space\widgets\HeaderControlsMenu;
use humhub\modules\space\widgets\Menu;
use humhub\modules\space\widgets\Sidebar as SpaceSidebar;
use humhub\modules\user\widgets\AccountMenu;
use humhub\modules\admin\widgets\AdminMenu;
use humhub\modules\user\widgets\AccountTopMenu;
use humhub\widgets\BaseMenu;
use humhub\widgets\FooterMenu;
use humhub\widgets\TopMenu;

return [
    'id' => 'custom_pages',
    'class' => 'humhub\modules\custom_pages\Module',
    'modules' => [
        'template' => [
            'class' => 'humhub\modules\custom_pages\modules\template\Module'
        ],
    ],
    'urlManagerRules' => [
        ['class' => 'humhub\modules\custom_pages\components\PageUrlRule']
    ],
    'namespace' => 'humhub\modules\custom_pages',
    'events' => [
        ['class' => Application::class, 'event' => Application::EVENT_BEFORE_REQUEST, 'callback' => [Events::class, 'onBeforeRequest']],
        ['class' => AdminMenu::class, 'event' => AdminMenu::EVENT_INIT, 'callback' => [Events::class, 'onAdminMenuInit']],
        ['class' => TopMenu::class, 'event' => TopMenu::EVENT_INIT, 'callback' => [Events::class, 'onTopMenuInit']],
        ['class' => AccountMenu::class, 'event' => AccountMenu::EVENT_INIT, 'callback' => [Events::class, 'onAccountMenuInit']],
        ['class' => Menu::class, 'event' => Menu::EVENT_INIT, 'callback' => [Events::class, 'onSpaceMenuInit']],
        ['class' => AccountTopMenu::class, 'event' => AccountTopMenu::EVENT_INIT, 'callback' => [Events::class, 'onAccountTopMenuInit']],
        ['class' => FooterMenu::class, 'event' => FooterMenu::EVENT_INIT, 'callback' => [Events::class, 'onFooterMenuInit']],
        ['class' => 'humhub\modules\user\widgets\PeopleHeadingButtons', 'event' => 'init', 'callback' => [Events::class, 'onPeopleHeadingButtonsInit']],

        ['class' => HeaderControlsMenu::class, 'event' => BaseMenu::EVENT_INIT, 'callback' => [Events::class, 'onSpaceAdminMenuInit']],

        ['class' => DashboardSidebar::class, 'event' => BaseMenu::EVENT_INIT, 'callback' => [Events::class, 'onDashboardSidebarInit']],
        ['class' => SpaceSidebar::class, 'event' => BaseMenu::EVENT_INIT, 'callback' => [Events::class, 'onSpaceSidebarInit']],
    ],
];