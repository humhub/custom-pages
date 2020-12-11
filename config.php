<?php

use humhub\modules\space\widgets\Menu;
use humhub\modules\user\widgets\AccountMenu;
use humhub\modules\admin\widgets\AdminMenu;
use humhub\modules\user\widgets\AccountTopMenu;
use humhub\widgets\BaseMenu;
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
        ['class' => AdminMenu::class, 'event' => AdminMenu::EVENT_INIT, 'callback' => ['humhub\modules\custom_pages\Events', 'onAdminMenuInit']],
        ['class' => TopMenu::class, 'event' => TopMenu::EVENT_INIT, 'callback' => ['humhub\modules\custom_pages\Events', 'onTopMenuInit']],
        ['class' => AccountMenu::class, 'event' => AccountMenu::EVENT_INIT, 'callback' => ['humhub\modules\custom_pages\Events', 'onAccountMenuInit']],
        ['class' => Menu::class, 'event' => Menu::EVENT_INIT, 'callback' => ['humhub\modules\custom_pages\Events', 'onSpaceMenuInit']],
        ['class' => AccountTopMenu::class, 'event' => AccountTopMenu::EVENT_INIT, 'callback' => ['humhub\modules\custom_pages\Events', 'onAccountTopMenuInit']],

        ['class' => 'humhub\modules\space\widgets\HeaderControlsMenu', 'event' => BaseMenu::EVENT_INIT, 'callback' => ['humhub\modules\custom_pages\Events', 'onSpaceAdminMenuInit']],
        ['class' => 'humhub\modules\directory\widgets\Menu', 'event' => BaseMenu::EVENT_INIT, 'callback' => ['humhub\modules\custom_pages\Events', 'onDirectoryMenuInit']],

        ['class' => 'humhub\modules\dashboard\widgets\Sidebar', 'event' => BaseMenu::EVENT_INIT, 'callback' => ['humhub\modules\custom_pages\Events', 'onDashboardSidebarInit']],
        ['class' => 'humhub\modules\directory\widgets\Sidebar', 'event' => BaseMenu::EVENT_INIT, 'callback' => ['humhub\modules\custom_pages\Events', 'onDirectorySidebarInit']],
        ['class' => 'humhub\modules\space\widgets\Sidebar', 'event' => BaseMenu::EVENT_INIT, 'callback' => ['humhub\modules\custom_pages\Events', 'onSpaceSidebarInit']],
    ],
];
?>
