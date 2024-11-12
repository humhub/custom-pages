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
 */
class Page extends CustomContentContainer
{
    /**
     * @inheritdoc
     */
    public $wallEntryClass = WallEntry::class;

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

        if ($this->isSnippet()) {
            $result['page_content'] = Yii::t('CustomPagesModule.model', 'Content');
            $result['target'] = Yii::t('CustomPagesModule.model', 'Sidebar');
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = $this->defaultRules();

        if ($this->isSnippet()) {
            return $rules;
        }

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
        if ($this->getTargetId() == PageType::TARGET_ACCOUNT_MENU) {
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
    public function getLabel(): string
    {
        return $this->isSnippet()
            ? Yii::t('CustomPagesModule.model', 'snippet')
            : Yii::t('CustomPagesModule.model', 'page');
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
        if ($this->isSnippet()) {
            return [
                MarkdownType::ID,
                IframeType::ID,
                TemplateType::ID,
                PhpType::ID,
                HtmlType::ID,
            ];
        }

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
        return Template::getSelection([
            'type' => $this->isSnippet() ? Template::TYPE_SNIPPED_LAYOUT : Template::TYPE_LAYOUT,
            'allow_for_spaces' => $this->isGlobal() ? 0 : 1,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getPhpViewPath()
    {
        $settings = new SettingsForm();

        if ($this->isSnippet()) {
            return $this->isGlobal()
                ? $settings->phpGlobalSnippetPath
                : $settings->phpContainerSnippetPath;
        }

        return $this->isGlobal()
            ? $settings->phpGlobalPagePath
            : $settings->phpContainerPagePath;
    }

    public function getEditUrl(): string
    {
        return $this->isSnippet()
            ? Url::toEditSnippet($this->id, $this->content->container)
            : Url::toEditPage($this->id, $this->content->container);
    }

    /**
     * @inheritdoc
     */
    public function getPageType(): string
    {
        return $this->isSnippet() ? PageType::Snippet : PageType::Page;
    }

    public function isSnippet(): bool
    {
        return $this->getTargetModel() && $this->getTargetModel()->isSnippet;
    }

    public function isGlobal(): bool
    {
        return !isset($this->content->container);
    }
}
