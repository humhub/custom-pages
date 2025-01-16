<?php

namespace humhub\modules\custom_pages;

use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\content\components\ContentContainerModule;
use humhub\modules\content\models\Content;
use humhub\modules\custom_pages\helpers\Url;
use humhub\modules\custom_pages\models\ContainerPage;
use humhub\modules\custom_pages\models\Page;
use humhub\modules\custom_pages\models\Snippet;
use humhub\modules\custom_pages\modules\template\models\AssetVariable;
use humhub\modules\custom_pages\modules\template\models\OwnerContentVariable;
use humhub\modules\space\models\Space;
use Yii;

class Module extends ContentContainerModule
{
    public const ICON = 'fa-file-text-o';

    public const SETTING_MIGRATION_KEY = 'global_pages_migrated_visibility';

    public $resourcesPath = 'resources';


    /**
     * @see https://twig.symfony.com/doc/3.x/api.html#sandbox-extension
     * @var bool
     */
    public $enableTwiqSandboxExtension = true;

    /**
     * @see https://twig.symfony.com/doc/3.x/api.html#sandbox-extension
     * @var array
     */
    public $enableTwiqSandboxExtensionConfig = [
        'allowedTags' => ['autoescape', 'apply', 'block', 'if', 'with', 'for', 'set'],
        'allowedFilters' => ['capitalize', 'date', 'first', 'upper', 'escape', 'nl2br', 'url_encode', 'round'],
        'allowedFunctions' => ['range', 'max', 'min', 'random'],
        'allowedMethods' => [
            'humhub\modules\custom_pages\modules\template\models\OwnerContentVariable' => '__toString',
        ],
        'allowedProperties' => [
            OwnerContentVariable::class => [
                'content',
                'emptyContent',
                'empty',
            ],
            AssetVariable::class => [
                'bgImage1.jpg',
                'bgImage2.jpg',
            ],
        ],
    ];

    public function checkOldGlobalContent()
    {

        if (!Yii::$app->user->isAdmin()) {
            return;
        }

        if (!$this->settings->get(static::SETTING_MIGRATION_KEY, 0)) {
            foreach (Page::find()->all() as $page) {
                $page->content->visibility = $page->admin_only ? Content::VISIBILITY_PRIVATE : Content::VISIBILITY_PUBLIC;
                $page->content->save();
            }

            foreach (Snippet::find()->all() as $snippet) {
                $snippet->content->visibility = $snippet->admin_only ? Content::VISIBILITY_PRIVATE : Content::VISIBILITY_PUBLIC;
                $snippet->content->save();
            }

            $this->settings->set(static::SETTING_MIGRATION_KEY, 1);
        }
    }

    /**
     * @inheritdoc
     */
    public function getConfigUrl()
    {
        return Url::toModuleConfig();
    }

    /**
     * @inheritdoc
     */
    public function disable()
    {
        foreach (Page::find()->all() as $page) {
            $page->hardDelete();
        }

        foreach (ContainerPage::find()->all() as $page) {
            $page->hardDelete();
        }

        foreach (models\Snippet::find()->all() as $page) {
            $page->hardDelete();
        }

        foreach (models\ContainerSnippet::find()->all() as $page) {
            $page->hardDelete();
        }

        parent::disable();
    }

    /**
     * @inheritdoc
     */
    public function getContentContainerTypes()
    {
        return [
            Space::class,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getContentClasses(): array
    {
        return [Page::class, ContainerPage::class];
    }

    /**
     * @inheritdoc
     */
    public function getContentContainerName(ContentContainerActiveRecord $container)
    {
        return Yii::t('CustomPagesModule.base', 'Custom pages');
    }

    public function getContentContainerDescription(ContentContainerActiveRecord $container)
    {
        if ($container instanceof Space) {
            return Yii::t('CustomPagesModule.base', 'Allows to add pages (markdown, iframe or links) to the space navigation');
        }
    }

    /**
     * @inheritdoc
     */
    public function disableContentContainer(ContentContainerActiveRecord $container)
    {
        parent::disableContentContainer($container);

        foreach (ContainerPage::find()->contentContainer($container)->all() as $page) {
            $page->hardDelete();
        }

        foreach (models\ContainerSnippet::find()->contentContainer($container)->all() as $page) {
            $page->hardDelete();
        }
    }

    /**
     * @inheritdoc
     */
    public function getPermissions($contentContainer = null)
    {
        if (!$contentContainer) {
            return [
                new permissions\ManagePages(),
            ];
        }

        return [];
    }
}
