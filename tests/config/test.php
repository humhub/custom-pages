<?php

return [
    #'humhub_root' => '...',
    'modules' => ['custom_pages'],
    'fixtures' => [
        'default',
        'template' => 'tests\codeception\fixtures\modules\custom_pages\template\TemplateFixture',
        'containerContent' => 'tests\codeception\fixtures\modules\custom_pages\template\ContainerContentFixture',
        'templateInstance' => 'tests\codeception\fixtures\modules\custom_pages\template\TemplateInstanceFixture',
        'templateElement' => 'tests\codeception\fixtures\modules\custom_pages\template\TemplateElementFixture',
        'richtextContent' => 'tests\codeception\fixtures\modules\custom_pages\template\RichtextContentFixture',
        'ownerContent' => 'tests\codeception\fixtures\modules\custom_pages\template\OwnerContentFixture',
        'containerContentDefinition' => 'tests\codeception\fixtures\modules\custom_pages\template\ContainerContentDefinitionFixture',
        'containerContentItem' => 'tests\codeception\fixtures\modules\custom_pages\template\ContainerContentItemFixture',
        'page' => 'tests\codeception\fixtures\modules\custom_pages\template\PageFixture',
        'snippet' => 'tests\codeception\fixtures\modules\custom_pages\template\SnippetFixture',
        'containerSnippet' => 'tests\codeception\fixtures\modules\custom_pages\template\ContainerSnippetFixture',
    ]
];



