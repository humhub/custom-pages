<?php

namespace humhub\modules\custom_pages\models;

use humhub\modules\custom_pages\helpers\Html;
use humhub\modules\custom_pages\helpers\Url;
use humhub\modules\custom_pages\models\forms\SettingsForm;
use humhub\modules\custom_pages\modules\template\models\Template;
use Yii;

/**
 * This is the model class for table "custom_pages_container_snipped".
 *
 * Snippets are custom sidebar panels which can be added to the dashboard sidebar.
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
class Snippet extends CustomContentContainer
{
    public const SIDEBAR_DASHBOARD = 'Dasboard';

    /**
     * @return string the associated database table name
     */
    public static function tableName()
    {
        return 'custom_pages_snippet';
    }

    /**
     * @inheritdoc
     * @return array
     */
    public function rules()
    {
        return $this->defaultRules();
    }

    /**
     * @inerhitdoc
     * @return array
     */
    public function attributeLabels()
    {
        $result = $this->defaultAttributeLabels();
        $result['page_content'] = Yii::t('CustomPagesModule.model', 'Content');
        $result['target'] = Yii::t('CustomPagesModule.model', 'Sidebar');
        return $result;
    }

    /**
     * Returns a sidebar selection for all sidebars this page can be added.
     * @return array
     */
    public static function getDefaultTargets()
    {
        return [
            ['id' => static::SIDEBAR_DASHBOARD, 'name' => Yii::t('CustomPagesModule.base', 'Dashboard'), 'accessRoute' => '/dashboard'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function getContentTypes()
    {
        return [
            MarkdownType::ID,
            IframeType::ID,
            TemplateType::ID,
            PhpType::ID,
            HtmlType::ID,
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
    public function getContentDescription()
    {
        return $this->title;
    }

    /**
     * @inheritdoc
     */
    public function getAllowedTemplateSelection()
    {
        return Template::getSelection(['type' => Template::TYPE_SNIPPED_LAYOUT]);
    }

    /**
     * @inheritdoc
     */
    public function getPageContent()
    {
        if ($this->type == HtmlType::ID) {
            return Html::applyScriptNonce($this->page_content);
        }

        return $this->page_content;
    }

    /**
     * @inheritdoc
     */
    public function getPhpViewPath()
    {
        $settings = new SettingsForm();
        return $settings->phpGlobalSnippetPath;
    }

    /**
     * @return string
     */
    public function getEditUrl()
    {
        return Url::toEditSnippet($this->id, $this->content->container);
    }

    /**
     * @return string
     */
    public function getPageType()
    {
        return PageType::Snippet;
    }
}
