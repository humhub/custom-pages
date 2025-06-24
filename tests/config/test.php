<?php

return [
    'modules' => ['custom_pages'],
    'fixtures' => [
        'default',
        'template' => 'tests\codeception\fixtures\modules\custom_pages\template\TemplateFixture',
        'templateInstance' => 'tests\codeception\fixtures\modules\custom_pages\template\TemplateInstanceFixture',
        'templateElement' => 'tests\codeception\fixtures\modules\custom_pages\template\TemplateElementFixture',
        'elementContentHtml' => 'tests\codeception\fixtures\modules\custom_pages\template\ElementContentHtmlFixture',
        'elementContentContainer' => 'tests\codeception\fixtures\modules\custom_pages\template\ElementContentContainerFixture',
        'elementContentContainerItem' => 'tests\codeception\fixtures\modules\custom_pages\template\ElementContentContainerItemFixture',
        'page' => 'tests\codeception\fixtures\modules\custom_pages\template\CustomPageFixture',
    ],
];
