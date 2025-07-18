<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages;

use humhub\components\ActiveRecord;
use humhub\libs\ProfileImage;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\content\components\ContentContainerModule;
use humhub\modules\content\models\Content;
use humhub\modules\custom_pages\helpers\Url;
use humhub\modules\custom_pages\models\CustomPage;
use humhub\modules\custom_pages\modules\template\elements\ContainerElementVariable;
use humhub\modules\custom_pages\modules\template\models\AssetVariable;
use humhub\modules\custom_pages\modules\template\elements\BaseElementVariable;
use humhub\modules\custom_pages\modules\template\services\TemplateImportService;
use humhub\modules\space\models\Space;
use SimpleXMLElement;
use Symfony\Component\String\UnicodeString;
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
    public bool $enableTwiqSandboxExtension = true;

    /**
     * Enable it only when required to edit default templates before
     * exporting and updating them in the folder "resources/templates/"
     * @var bool
     */
    public bool $allowUpdateDefaultTemplates = false;

    /**
     * @see https://twig.symfony.com/doc/3.x/api.html#sandbox-extension
     * @var array
     */
    public array $enableTwiqSandboxExtensionConfig = [
        'allowedTags' => ['autoescape', 'apply', 'block', 'if', 'with', 'for', 'set'],
        'allowedFilters' => ['capitalize', 'date', 'first', 'slice', 'upper', 'escape',
            'raw', 'nl2br', 'url_encode', 'round', 'u', 'striptags',
            'formatter_as_date', 'formatter_as_time', 'formatter_as_date_time',
            'markdown_strip', 'markdown_html', 'markdown_plain', 'markdown_short'],
        'allowedFunctions' => ['range', 'max', 'min', 'random'],
        'allowedMethods' => [
            BaseElementVariable::class => [
                '__toString',
            ],
            UnicodeString::class => [
                '__toString',
                'truncate',
            ],
            ContentContainerActiveRecord::class => [
                'getUrl',
            ],
            ProfileImage::class => [
                'getUrl',
            ],
        ],
        'allowedProperties' => [
            BaseElementVariable::class => '*',
            AssetVariable::class => [
                'bgImage1.jpg',
                'bgImage2.jpg',
            ],
            SimpleXMLElement::class => '*',
            ActiveRecord::class => '*',
            ProfileImage::class => '*',
        ],
    ];

    public function checkOldGlobalContent()
    {
        if (!Yii::$app->user->isAdmin()) {
            return;
        }

        if (!$this->settings->get(static::SETTING_MIGRATION_KEY, 0)) {
            foreach (CustomPage::find()->all() as $page) {
                /* @var CustomPage $page */
                $page->content->visibility = $page->admin_only ? Content::VISIBILITY_PRIVATE : Content::VISIBILITY_PUBLIC;
                $page->content->save();
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
        foreach (CustomPage::find()->all() as $page) {
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
        return [CustomPage::class];
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

        foreach (CustomPage::find()->contentContainer($container)->all() as $page) {
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

    /**
     * @inheritdoc
     */
    public function enable()
    {
        return parent::enable() && TemplateImportService::instance()->importDefaultTemplates();
    }

    /**
     * @inheritdoc
     */
    public function update()
    {
        parent::update();
        TemplateImportService::instance()->importDefaultTemplates();
    }
}
