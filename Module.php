<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages;

use humhub\libs\ProfileImage;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\content\components\ContentContainerModule;
use humhub\modules\content\models\Content;
use humhub\modules\custom_pages\helpers\Url;
use humhub\modules\custom_pages\models\CustomPage;
use humhub\modules\custom_pages\modules\template\models\AssetVariable;
use humhub\modules\custom_pages\modules\template\elements\BaseElementVariable;
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
    public $enableTwiqSandboxExtension = true;

    /**
     * @see https://twig.symfony.com/doc/3.x/api.html#sandbox-extension
     * @var array
     */
    public $enableTwiqSandboxExtensionConfig = [
        'allowedTags' => ['autoescape', 'apply', 'block', 'if', 'with', 'for', 'set'],
        'allowedFilters' => ['capitalize', 'date', 'first', 'upper', 'escape', 'raw', 'nl2br', 'url_encode', 'round', 'u', 'striptags'],
        'allowedFunctions' => ['range', 'max', 'min', 'random'],
        'allowedMethods' => [
            BaseElementVariable::class => [
                '__toString',
                'items',
                'profile',
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
            BaseElementVariable::class => [
                'content',
                'emptyContent',
                'empty',
            ],
            AssetVariable::class => [
                'bgImage1.jpg',
                'bgImage2.jpg',
            ],
            SimpleXMLElement::class => '*',
            ContentContainerActiveRecord::class => '*',
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
}
