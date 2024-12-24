<?php

return [
    'modules' => ['custom_pages'],
    'fixtures' => [
        'default',
        'template' => 'tests\codeception\fixtures\modules\custom_pages\template\TemplateFixture',
        'containerContent' => 'tests\codeception\fixtures\modules\custom_pages\template\ContainerContentFixture',
        'templateInstance' => 'tests\codeception\fixtures\modules\custom_pages\template\TemplateInstanceFixture',
        'templateElement' => 'tests\codeception\fixtures\modules\custom_pages\template\TemplateElementFixture',
        'richtextElement' => 'tests\codeception\fixtures\modules\custom_pages\template\RichtextElementFixture',
        'ownerContent' => 'tests\codeception\fixtures\modules\custom_pages\template\OwnerContentFixture',
        'containerContentDefinition' => 'tests\codeception\fixtures\modules\custom_pages\template\ContainerContentDefinitionFixture',
        'containerContentTemplate' => \tests\codeception\fixtures\modules\custom_pages\template\ContainerContentTemplateFixture::class,
        'containerContentItem' => 'tests\codeception\fixtures\modules\custom_pages\template\ContainerContentItemFixture',
        'page' => 'tests\codeception\fixtures\modules\custom_pages\template\CustomPageFixture',
    ],
];
