<?php

Yii::app()->moduleManager->register(array(
    'id' => 'custom_pages',
    'class' => 'application.modules.custom_pages.CustomPagesModule',
    'import' => array(
        'application.modules.custom_pages.*',
        'application.modules.custom_pages.models.*',
    ),
    // Events to Catch 
    'events' => array(
        array('class' => 'AdminMenuWidget', 'event' => 'onInit', 'callback' => array('CustomPagesEvents', 'onAdminMenuInit')),
        array('class' => 'TopMenuWidget', 'event' => 'onInit', 'callback' => array('CustomPagesEvents', 'onTopMenuInit')),
    ),
));
?>