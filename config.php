<?php

use humhub\modules\user\widgets\AccountMenu;
use humhub\modules\admin\widgets\AdminMenu;
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
        ['class' => AdminMenu::className(), 'event' => AdminMenu::EVENT_INIT, 'callback' => ['humhub\modules\custom_pages\Events', 'onAdminMenuInit']],
        ['class' => TopMenu::className(), 'event' => TopMenu::EVENT_INIT, 'callback' => ['humhub\modules\custom_pages\Events', 'onTopMenuInit']],
        ['class' => AccountMenu::className(), 'event' => AccountMenu::EVENT_INIT, 'callback' => ['humhub\modules\custom_pages\Events', 'onAccountMenuInit']],
        ['class' => humhub\modules\space\widgets\Menu::className(), 'event' => humhub\modules\space\widgets\Menu::EVENT_INIT, 'callback' => ['humhub\modules\custom_pages\Events', 'onSpaceMenuInit']],

        ['class' => 'humhub\modules\space\widgets\HeaderControlsMenu', 'event' => BaseMenu::EVENT_INIT, 'callback' => ['humhub\modules\custom_pages\Events', 'onSpaceAdminMenuInit']],
        //['class' => 'humhub\modules\space\widgets\HeaderControls', 'event' => BaseMenu::EVENT_INIT, 'callback' => ['humhub\modules\custom_pages\Events', 'onSpaceHeaderMenuInit']],
        ['class' => 'humhub\modules\directory\widgets\Menu', 'event' => BaseMenu::EVENT_INIT, 'callback' => ['humhub\modules\custom_pages\Events', 'onDirectoryMenuInit']],
        
        ['class' => 'humhub\modules\dashboard\widgets\Sidebar', 'event' => BaseMenu::EVENT_INIT, 'callback' => ['humhub\modules\custom_pages\Events', 'onDashboardSidebarInit']],
        ['class' => 'humhub\modules\directory\widgets\Sidebar', 'event' => BaseMenu::EVENT_INIT, 'callback' => ['humhub\modules\custom_pages\Events', 'onDirectorySidebarInit']],
        ['class' => 'humhub\modules\space\widgets\Sidebar', 'event' => BaseMenu::EVENT_INIT, 'callback' => ['humhub\modules\custom_pages\Events', 'onSpaceSidebarInit']],
    ],
];
?>