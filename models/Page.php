<?php

namespace humhub\modules\custom_pages\models;

use humhub\modules\content\models\Content;
use humhub\modules\custom_pages\helpers\Html;
use humhub\modules\custom_pages\helpers\Url;
use humhub\modules\custom_pages\models\forms\SettingsForm;
use humhub\modules\custom_pages\modules\template\components\TemplateRenderer;
use humhub\modules\custom_pages\modules\template\models\Template;
use humhub\modules\custom_pages\widgets\WallEntry;
use Yii;

/**
 * This is the model class for table "custom_pages_page".
 *
 * Pages are global custom page container which can be added to the main navigation or
 * user account setting navigation.
 *
 * The followings are the available columns in table 'custom_pages_page':
 * @property int $id
 * @property boolean $is_snippet
 * @property int $type
 * @property string $title
 * @property string $icon
 * @property string $page_content
 * @property string $iframe_attrs
 * @property int $sort_order
 * @property int $admin_only
 * @property int $in_new_window
 * @property string $target
 * @property string $cssClass
 * @property string $url
 * @property string $abstract
 * @method string viewTemplatePage(CustomContentContainer $page)
 */
class Page extends CustomContentContainer
{
    /**
     * @inheritdoc
     */
    public $wallEntryClass = WallEntry::class;

    public const NAV_CLASS_TOPNAV = 'TopMenuWidget';
    public const NAV_CLASS_ACCOUNTNAV = 'AccountMenuWidget';
    public const NAV_CLASS_EMPTY = 'WithOutMenu';
    public const NAV_CLASS_FOOTER = 'FooterMenuWidget';
    public const NAV_CLASS_PEOPLE = 'PeopleButtonsWidget';
    public const TARGET_DASHBOARD = 'Dashboard';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if ($this->isNewRecord && $this->type == HtmlType::ID &&
            ($this->page_content === null || $this->page_content === '')) {
            $this->page_content = '<div class="panel panel-default"><div class="panel-body"></div></div>';
        }
    }

    /**
     * @return string the associated database table name
     */
    public static function tableName()
    {
        return 'custom_pages_page';
    }

    /**
     * @inerhitdoc
     * @return array
     */
    public function attributeLabels()
    {
        $result = $this->defaultAttributeLabels();
        $result['in_new_window'] = Yii::t('CustomPagesModule.model', 'Open in new window');
        $result['abstract'] = Yii::t('CustomPagesModule.model', 'Abstract');
        $result['target'] = Yii::t('CustomPagesModule.model', 'Navigation');
        $result['url'] = Yii::t('CustomPagesModule.model', 'Url shortcut');
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = $this->defaultRules();

        $target = $this->getTargetModel();
        if ($target && $target->isAllowedField('in_new_window')) {
            $rules[] = [['in_new_window'], 'integer'];
        }

        if ($target && $target->isAllowedField('abstract')) {
            $rules[] = [['abstract'], 'string'];
        }

        if ($target && $target->isAllowedField('url')) {
            $rules[] = [['url'], 'string'];
        }

        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        $this->fixVisibility();
        return parent::beforeSave($insert);
    }

    /**
     * @inheritdoc
     */
    public function fixVisibility()
    {
        // Force visibility access "Members & Guests" to "Members only" for
        // page type "User Account Menu (Settings)"
        if ($this->getTargetId() == Page::NAV_CLASS_ACCOUNTNAV) {
            if ($this->visibility == CustomContentContainer::VISIBILITY_PUBLIC) {
                $this->visibility = CustomContentContainer::VISIBILITY_PRIVATE;
            }
            if ($this->content->visibility == Content::VISIBILITY_PUBLIC) {
                $this->content->visibility = Content::VISIBILITY_PRIVATE;
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function getContentDescription()
    {
        return $this->title;
    }

    /**
     * Returns a container specific title mainly used in views.
     * @return string
     */
    public function getLabel()
    {
        return Yii::t('CustomPagesModule.model', 'page');
    }

    /**
     * Returns a navigation selection for all navigations this page can be added.
     * @return array
     */
    public static function getDefaultTargets(string $type = 'page')
    {
        if ($type === 'snippet') {
            return [
                ['id' => self::TARGET_DASHBOARD, 'name' => Yii::t('CustomPagesModule.base', 'Dashboard'), 'accessRoute' => '/dashboard'],
            ];
        }

        $targets = [
            ['id' => self::NAV_CLASS_TOPNAV, 'name' => Yii::t('CustomPagesModule.base', 'Top Navigation')],
            ['id' => self::NAV_CLASS_ACCOUNTNAV, 'name' => Yii::t('CustomPagesModule.base', 'User Account Menu (Settings)'), 'subLayout' => '@humhub/modules/user/views/account/_layout'],
            ['id' => self::NAV_CLASS_EMPTY, 'name' => Yii::t('CustomPagesModule.base', 'Without adding to navigation (Direct link)')],
            ['id' => self::NAV_CLASS_FOOTER, 'name' => Yii::t('CustomPagesModule.base', 'Footer menu')],
        ];

        if (class_exists('humhub\modules\user\widgets\PeopleHeadingButtons')) {
            $targets[] = ['id' => self::NAV_CLASS_PEOPLE, 'name' => Yii::t('CustomPagesModule.base', 'People Buttons')];
        }

        return $targets;
    }

    /**
     * @inheritdoc
     */
    public function getSearchAttributes()
    {
        return [
            'title' => $this->title,
            'content' => preg_replace('/[\r\n\s]+/', ' ', strip_tags($this->type === TemplateType::ID
                ? TemplateRenderer::render($this, false, false, true)
                : $this->abstract . "\r\n" . $this->page_content)),
        ];
    }

    /**
     * Returns an array of all allowed conten types for this container type.
     * @return int[]
     */
    public function getContentTypes()
    {
        return [
            LinkType::ID,
            HtmlType::ID,
            MarkdownType::ID,
            IframeType::ID,
            TemplateType::ID,
            PhpType::ID,
        ];
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
    public function getAllowedTemplateSelection()
    {
        return Template::getSelection(['type' => Template::TYPE_LAYOUT]);
    }

    /**
     * @inheritdoc
     */
    public function getPhpViewPath()
    {
        return (new SettingsForm())->phpGlobalPagePath;
    }


    /**
     * @return string
     */
    public function getEditUrl()
    {
        return Url::toEditPage($this->id, $this->content->container);
    }

    /**
     * @return string
     */
    public function getPageType()
    {
        return empty($this->is_snippet) ? PageType::Page : PageType::Snippet;
    }
}
