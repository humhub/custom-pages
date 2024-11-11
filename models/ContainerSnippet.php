<?php

namespace humhub\modules\custom_pages\models;

use humhub\modules\custom_pages\models\forms\SettingsForm;
use humhub\modules\custom_pages\modules\template\models\Template;
use humhub\modules\space\models\Space;
use Yii;

/**
 * This is the model class for table "custom_pages_container_snipped".
 *
 * ContainerSnippets are snippets which can be added to a space sidebar.
 *
 * The followings are the available columns in table 'custom_pages_container_page':
 * @property int $id
 * @property int $type
 * @property string $title
 * @property string $icon
 * @property string $page_content
 * @property string $iframe_attrs
 * @property int $sort_order
 * @property int $admin_only
 * @property string $cssClass
 */
class ContainerSnippet extends Snippet
{
    public const SIDEEBAR_STREAM = 'SpaceStreamSidebar';

    /**
     * @return string the associated database table name
     */
    public static function tableName()
    {
        return 'custom_pages_container_snippet';
    }

    public static function getDefaultTargets(string $type = 'page')
    {
        return [
            ['id' => self::SIDEEBAR_STREAM, 'name' => Yii::t('CustomPagesModule.base', 'Stream'), 'accessRoute' => '/space/space/home'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function getLabel()
    {
        return Yii::t('CustomPagesModule.model', 'snippet');
    }

    /**
     * @inheritdoc
     */
    public function getAllowedTemplateSelection()
    {
        return Template::getSelection(['type' => Template::TYPE_SNIPPED_LAYOUT, 'allow_for_spaces' => 1]);
    }

    /**
     * @inheritdoc
     */
    public function getPhpViewPath()
    {
        return (new SettingsForm())->phpContainerSnippetPath;
    }


    /**
     * @inheritdoc
     */
    public function getVisibilitySelection()
    {
        $result = [
            static::VISIBILITY_ADMIN_ONLY => Yii::t('CustomPagesModule.base', 'Admin only'),
            static::VISIBILITY_PRIVATE => Yii::t('CustomPagesModule.base', 'Space Members only'),
        ];

        $container = $this->content->container;
        if ($container->visibility != Space::VISIBILITY_NONE) {
            $result[static::VISIBILITY_PUBLIC] = Yii::t('CustomPagesModule.base', 'Public');
        }

        return $result;
    }
}
