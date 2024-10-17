<?php

namespace humhub\modules\custom_pages\models;

use humhub\modules\space\models\Space;
use Yii;
use humhub\modules\custom_pages\helpers\Url;
use humhub\modules\custom_pages\models\forms\SettingsForm;
use humhub\modules\search\interfaces\Searchable;
use humhub\modules\custom_pages\modules\template\models\Template;

/**
 * This is the model class for table "custom_pages_container_page".
 *
 * A container page is space related custom page container.
 */
class ContainerPage extends Page implements Searchable
{
    public const NAV_CLASS_SPACE_NAV = 'SpaceMenu';

    /**
     * Returns a navigation selection for all navigations this page can be added.
     * @return array
     */
    public static function getDefaultTargets()
    {
        return [
            ['id' => self::NAV_CLASS_SPACE_NAV , 'name' => Yii::t('CustomPagesModule.base', 'Space Navigation')],
            ['id' => self::NAV_CLASS_EMPTY, 'name' => Yii::t('CustomPagesModule.base', 'Without adding to navigation (Direct link)')],
        ];
    }

    /**
     * @return string the associated database table name
     */
    public static function tableName()
    {
        return 'custom_pages_container_page';
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        $result = $this->defaultAttributeLabels();
        $result['in_new_window'] = Yii::t('CustomPagesModule.model', 'Open in new window');

        if (PhpType::isType($this->getContentType())) {
            $contentLabel = Yii::t('CustomPagesModule.model', 'View');
        } else {
            $contentLabel = Yii::t('CustomPagesModule.base', 'Content');
        }

        $result['page_content'] = $contentLabel;
        $result['admin_only'] = Yii::t('CustomPagesModule.model', 'Only visible for space admins');
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function getContentTypes()
    {
        return [
            MarkdownType::ID,
            LinkType::ID,
            IframeType::ID,
            TemplateType::ID,
            PhpType::ID,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getAllowedTemplateSelection()
    {
        return Template::getSelection(['type' => Template::TYPE_LAYOUT, 'allow_for_spaces' => 1]);
    }

    /**
     * @inheritdoc
     */
    public function getPhpViewPath()
    {
        $settings = new SettingsForm();
        return $settings->phpContainerPagePath;
    }

    /**
     * @return string
     */
    public function getEditUrl()
    {
        return Url::toEditPage($this->id, $this->content->container);
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
