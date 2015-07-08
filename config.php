<?php

use module\custom_pages\Events;
use humhub\modules\user\widgets\AccountMenu;
use humhub\modules\admin\widgets\AdminMenu;
use humhub\widgets\TopMenu;

return [
    'id' => 'custom_pages',
    'class' => 'module\custom_pages\Module',
    'events' => [
        ['class' => AdminMenu::className(), 'event' => AdminMenu::EVENT_INIT, 'callback' => [Events::className(), 'onAdminMenuInit']],
        ['class' => TopMenu::className(), 'event' => TopMenu::EVENT_INIT, 'callback' => [Events::className(), 'onTopMenuInit']],
        ['class' => AccountMenu::className(), 'event' => AccountMenu::EVENT_INIT, 'callback' => [Events::className(), 'onAccountMenuInit']],
    ],
];
?>