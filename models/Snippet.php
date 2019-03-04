<?php

namespace humhub\modules\custom_pages\models;

use Yii;
use humhub\modules\custom_pages\helpers\Url;
use humhub\modules\custom_pages\models\forms\SettingsForm;
use humhub\modules\custom_pages\modules\template\models\Template;

/**
 * This is the model class for table "custom_pages_container_snipped".
 *
 * Snippets are custom sidebar panels which can be added to the directory/dashboard sidebar.
 * 
 * The followings are the available columns in table 'custom_pages_container_page':
 * @property integer $id
 * @property integer $type
 * @property string $title
 * @property string $icon
 * @property string $page_content
 * @property integer $sort_order
 * @property integer $admin_only
 * @property string $cssClass
 */
class Snippet extends CustomContentContainer
{

    const SIDEBAR_DASHBOARD = 'Dasboard';
    const SIDEBAR_DIRECTORY = 'Directory';

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
        $result['page_content'] = Yii::t('CustomPagesModule.models_Snippet', 'Content');
        $result['target'] = Yii::t('CustomPagesModule.models_Snippet', 'Sidebar');
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
            ['id' => static::SIDEBAR_DIRECTORY, 'name' => Yii::t('CustomPagesModule.base', 'Directory'), 'accessRoute' => '/directory/directory']
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
        ];
    }

    /**
     * @inheritdoc
     */
    public function getLabel()
    {
        return Yii::t('CustomPagesModule.models_Snippet', 'snippet');
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
