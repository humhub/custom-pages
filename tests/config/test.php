<?php

return [
    'modules' => ['custom_pages'],
    'fixtures' => [
        'default',
        'template' => 'tests\codeception\fixtures\modules\custom_pages\template\TemplateFixture',
        'templateInstance' => 'tests\codeception\fixtures\modules\custom_pages\template\TemplateInstanceFixture',
        'templateElement' => 'tests\codeception\fixtures\modules\custom_pages\template\TemplateElementFixture',
        'elementContentRichtext' => 'tests\codeception\fixtures\modules\custom_pages\template\ElementContentRichtextFixture',
        'elementContentContainer' => 'tests\codeception\fixtures\modules\custom_pages\template\ElementContentContainerFixture',
        'elementContentDefinitionContainer' => 'tests\codeception\fixtures\modules\custom_pages\template\ElementContentDefinitionContainerFixture',
        'elementContentContainerItem' => 'tests\codeception\fixtures\modules\custom_pages\template\ElementContentContainerItemFixture',
        'ownerContent' => 'tests\codeception\fixtures\modules\custom_pages\template\OwnerContentFixture',
        'page' => 'tests\codeception\fixtures\modules\custom_pages\template\CustomPageFixture',
    ],
];
