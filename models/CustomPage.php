<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\models;

use humhub\interfaces\ViewableInterface;
use humhub\modules\content\components\ContentActiveRecord;
use humhub\modules\content\widgets\richtext\RichText;
use humhub\modules\custom_pages\components\PhpPageContainer;
use humhub\modules\custom_pages\components\TemplatePageContainer;
use humhub\modules\custom_pages\helpers\Html;
use humhub\modules\custom_pages\helpers\PageType;
use humhub\modules\custom_pages\helpers\Url;
use humhub\modules\custom_pages\interfaces\CustomPagesService;
use humhub\modules\custom_pages\models\forms\SettingsForm;
use humhub\modules\custom_pages\modules\template\models\Template;
use humhub\modules\custom_pages\modules\template\models\TemplateInstance;
use humhub\modules\custom_pages\modules\template\services\TemplateInstanceRendererService;
use humhub\modules\custom_pages\permissions\ManagePages;
use humhub\modules\custom_pages\services\SettingService;
use humhub\modules\custom_pages\services\VisibilityService;
use humhub\modules\custom_pages\types\ContentType;
use humhub\modules\custom_pages\types\HtmlType;
use humhub\modules\custom_pages\types\IframeType;
use humhub\modules\custom_pages\types\LinkType;
use humhub\modules\custom_pages\types\MarkdownType;
use humhub\modules\custom_pages\types\PhpType;
use humhub\modules\custom_pages\types\TemplateType;
use humhub\modules\custom_pages\widgets\WallEntry;
use humhub\modules\space\models\Space;
use LogicException;
use Yii;

/**
 * This is the model class for table "custom_pages_page".
 *
 * The following columns are in table 'custom_pages_page':
 * @property int $id
 * @property int $type
 * @property string $title
 * @property string $icon
 * @property string $page_content
 * @property string $iframe_attrs
 * @property int $sort_order
 * @property string $target
 * @property int $visibility
 * @property bool $in_new_window
 * @property string $cssClass
 * @property string $url
 * @property string $abstract
 *
 * @property-read VisibilityService $visibilityService
 * @property-read SettingService $settingService
 */
class CustomPage extends ContentActiveRecord implements ViewableInterface
{
    use PhpPageContainer;
    use TemplatePageContainer;

    // Content Visibility = Public:
    public const VISIBILITY_PUBLIC = 1; // Always
    public const VISIBILITY_GUEST = 4; // Non-Logged-In Users
    // Content Visibility = Private:
    public const VISIBILITY_PRIVATE = 0; // Logged-In Users
    public const VISIBILITY_ADMIN = 3; // Administrative Users
    public const VISIBILITY_CUSTOM = 5; // Custom

    /**
     * @inheritdoc
     */
    public $silentContentCreation = true;

    /**
     * @var Target cached target
     */
    private $_target;

    /**
     * @var int|null special field for template based pages specifying the layout template id
     */
    public ?int $templateId = null;

    /**
     * @var array Groups for custom visibility restriction
     */
    public $visibility_groups;

    /**
     * @var array Languages for custom visibility restriction
     */
    public $visibility_languages;

    private ?VisibilityService $_visibilityService = null;
    private ?SettingService $_settingService = null;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'custom_pages_page';
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->visibilityService->initDefault();

        if (!$this->isSnippet()) {
            $this->wallEntryClass = WallEntry::class;

            if ($this->isNewRecord && $this->type == HtmlType::ID &&
                ($this->page_content === null || $this->page_content === '')) {
                $this->page_content = '<div class="panel panel-default"><div class="panel-body"></div></div>';
            }
        }
    }

    /**
     * @inerhitdoc
     */
    public function attributeLabels()
    {
        $result = [
            'id' => Yii::t('CustomPagesModule.base', 'ID'),
            'type' => Yii::t('CustomPagesModule.base', 'Type'),
            'title' => Yii::t('CustomPagesModule.base', 'Title'),
            'icon' => Yii::t('CustomPagesModule.base', 'Icon'),
            'page_content' => Yii::t('CustomPagesModule.base', 'Page Content'),
            'iframe_attrs' => Yii::t('CustomPagesModule.base', 'Additional IFrame Attributes'),
            'sort_order' => Yii::t('CustomPagesModule.base', 'Sort Order'),
            'target' => Yii::t('CustomPagesModule.base', 'Category'),
            'in_new_window' => Yii::t('CustomPagesModule.model', 'Open in new window'),
            'cssClass' => Yii::t('CustomPagesModule.base', 'Style Class'),
            'url' => Yii::t('CustomPagesModule.model', 'Url shortcut'),
            'abstract' => Yii::t('CustomPagesModule.model', 'Abstract'),
            'content' => $this->getContentType() ? $this->getContentType()->getLabel() : null,
            'targetUrl' => Yii::t('CustomPagesModule.base', 'Target Url'),
            'templateId' => Yii::t('CustomPagesModule.base', 'Template Layout'),
            'visibility' => Yii::t('CustomPagesModule.model', 'Visibility'),
            'visibility_groups' => Yii::t('CustomPagesModule.model', 'Visible to Group Members'),
            'visibility_languages' => Yii::t('CustomPagesModule.model', 'Language-Based Visibility'),
        ];

        if ($this->isSnippet()) {
            // Any(global + space) Snippet
            $result['page_content'] = Yii::t('CustomPagesModule.model', 'Content');
        } elseif (!$this->isGlobal()) {
            // Only space Page
            $result['page_content'] = PhpType::isType($this->getContentType())
                ? Yii::t('CustomPagesModule.model', 'View')
                : Yii::t('CustomPagesModule.base', 'Content');
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = [
            [['type', 'title', 'target', 'visibility'], 'required'],
            [['type'], 'integer'],
            [['target'], 'validateTarget'],
            [['type'], 'validateContentType'],
            [['visibility'], 'integer', 'min' => self::VISIBILITY_PRIVATE, 'max' => self::VISIBILITY_CUSTOM],
            [['visibility_groups'], 'safe'],
            [['visibility_languages'], 'safe'],
            [['title', 'target'], 'string', 'max' => 255],
        ];

        return array_merge($rules, $this->getRulesByTarget(), $this->getRulesByContentType());
    }

    private function getRulesByContentType(): array
    {
        $rules = [];
        $type = $this->getContentType();

        if (!$type) {
            return $rules;
        }

        if ((IframeType::isType($type) && Yii::$app->user->isAdmin())) {
            // Allow System Admins to modify the Iframe Attrs
            $rules[] = [['iframe_attrs'], 'string', 'max' => 255];
        }

        if (PhpType::isType($type) ||
            LinkType::isType($type) ||
            HtmlType::isType($type) ||
            MarkdownType::isType($type) ||
            (IframeType::isType($type) && Yii::$app->user->isAdmin())) {
            $rules[] = [['page_content'], 'required'];
        }

        if (PhpType::isType($type)) {
            $rules[] = [['type'], 'validatePhpType'];
        }

        if (TemplateType::isType($type)) {
            $rules[] = [['templateId'], 'safe'];
            $rules[] = [['type'], 'validateTemplateType'];
        }

        if ($type->hasContent()) {
            $rules[] = [['page_content'], 'safe'];
        }

        return $rules;
    }

    private function getRulesByTarget(): array
    {
        $rules = [];
        $target = $this->getTargetModel();

        if (!$target) {
            return $rules;
        }

        if ($target->isAllowedField('sort_order')) {
            $rules[] = [['sort_order'], 'integer'];
        }

        if ($target->isAllowedField('icon')) {
            $rules[] = [['icon'], 'string', 'max' => 100];
        }

        if ($target->isAllowedField('cssClass') && !LinkType::isType($this->getContentType())) {
            $rules[] = [['cssClass'], 'string', 'max' => 255];
        }

        if (!$this->isSnippet()) {
            if ($target->isAllowedField('in_new_window')) {
                $rules[] = [['in_new_window'], 'integer'];
            }

            if ($target->isAllowedField('abstract')) {
                $rules[] = [['abstract'], 'string'];
            }

            if ($target->isAllowedField('url')) {
                $rules[] = [['url'], 'string'];
            }
        }

        return $rules;
    }

    public function validateTarget()
    {
        if (!$this->getTargetModel()) {
            $this->addError('target', 'Target not available for this page container.');
        }
    }

    public function validateContentType()
    {
        $target = $this->getTargetModel();
        if ($target && !$target->isAllowedContentType($this->type)) {
            $this->addError('target', 'The selected content type is not allowed for this target.');
        }
    }

    /**
     * @inheritdoc
     */
    public function afterFind()
    {
        parent::afterFind();

        $this->visibilityService->loadAdditionalOptions();
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        $this->visibilityService->fix();

        return parent::beforeSave($insert);
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if (!$this->getContentType()->afterSave($this, $insert, $changedAttributes)) {
            throw new LogicException('Could not save content type ' . $this->getContentType()->getLabel());
        }

        if ($this->hasAbstract()) {
            RichText::postProcess($this->abstract, $this);
        }

        $this->visibilityService->updateAdditionalOptions();
    }

    /**
     * @inheritdoc
     */
    public function beforeDelete()
    {
        $this->getContentType()->afterDelete($this);
        return parent::beforeDelete();
    }

    public function hasAbstract(): bool
    {
        return !empty($this->abstract) && $this->isAllowedField('abstract');
    }

    /**
     * @inheritdoc
     */
    public function getContentName()
    {
        $target = $this->getTargetModel();
        if ($target && $target->contentName) {
            return $target->contentName;
        }

        $containerClass = $this->isGlobal() ? null : Space::class;

        return PageType::getContentName($this->getPageType(), $containerClass);
    }

    /**
     * @inheritdoc
     */
    public function getContentDescription()
    {
        return $this->canView()
            ? $this->title
            : Yii::t('CustomPagesModule.view', 'You don\'t have permission to access the page');
    }

    /**
     * Returns all allowed content types for a page container class.
     *
     * @return ContentType|null
     */
    public function getContentType(): ?ContentType
    {
        return ContentType::getByPage($this);
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getIcon(): ?string
    {
        if ($this->hasAttribute('icon') && $this->icon) {
            return $this->icon;
        }

        return $this->getTargetModel()?->getIcon();
    }

    /**
     * Returns a container specific title mainly used in views.
     *
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
                ? TemplateInstanceRendererService::instance($this)->disableScriptNonce()->ignoreCache()->render()
                : $this->abstract . "\r\n" . $this->page_content)),
        ];
    }

    /**
     * Returns the view url of this page.
     *
     * @return string
     */
    public function getUrl(): string
    {
        return $this->getTargetModel()->getContentUrl($this);
    }

    /**
     * Returns an array of all allowed content types for this Page.
     *
     * @return int[]
     */
    public function getContentTypes(): array
    {
        $types = [
            MarkdownType::ID,
            IframeType::ID,
            TemplateType::ID,
            PhpType::ID,
        ];

        if (!$this->isSnippet()) {
            // Only for Page
            $types[] = LinkType::ID;
        }

        if ($this->isSnippet() || $this->isGlobal()) {
            // For any(global & space) Snippet or global Page
            $types[] = HtmlType::ID;
        }

        return $types;
    }

    /**
     * Returns the database content field. Note this does not render any content.
     *
     * @return string
     */
    public function getPageContent(): string
    {
        if ($this->type == HtmlType::ID) {
            return Html::applyScriptNonce($this->page_content);
        }

        return $this->page_content;
    }

    /**
     * Returns all allowed templates for a page container class.
     *
     * @return array
     */
    public function getAllowedTemplateSelection(): array
    {
        $condition = ['type' => $this->isSnippet() ? Template::TYPE_SNIPPET_LAYOUT : Template::TYPE_LAYOUT];

        if (!$this->isGlobal()) {
            $condition['allow_for_spaces'] = 1;
        }

        return Template::getSelection($condition);
    }

    /**
     * Returns the view file path for PHP based content.
     *
     * @return string
     */
    public function getPhpViewPath(): string
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
     * Get type of the Custom Page: Page or Snippet
     *
     * @return string
     */
    public function getPageType(): string
    {
        return $this->isSnippet() ? PageType::Snippet : PageType::Page;
    }

    public function getTargetModel(): ?Target
    {
        if (!$this->_target) {
            $this->_target = CustomPagesService::instance()->getTargetByPage($this);
        }

        return $this->_target;
    }

    public function getTargetId(): ?string
    {
        return $this->target;
    }

    public function getAvailableTargetOptions(): array
    {
        $targets = CustomPagesService::instance()->getTargets($this->getPageType(), $this->content->container);
        return array_column($targets, 'name', 'id');
    }

    public function hasTarget($targetId): bool
    {
        return $this->target === $targetId;
    }

    public function getTemplateInstance(): ?TemplateInstance
    {
        return TemplateInstance::findByOwner($this);
    }

    public function getTemplateId(): ?int
    {
        if ($this->templateId === null) {
            $templateInstance = $this->getTemplateInstance();
            $this->templateId = $templateInstance ? $templateInstance->template_id : 0;
        }

        return $this->templateId;
    }

    public function isSnippet(): bool
    {
        return $this->getTargetModel() && $this->getTargetModel()->type === PageType::Snippet;
    }

    public function isGlobal(): bool
    {
        return !isset($this->content->container);
    }

    public function canEdit($type = null): bool
    {
        if (!($this->content->container instanceof Space && $this->content->container->isAdmin()) &&
            !Yii::$app->user->can(ManagePages::class)) {
            return false;
        }

        if (!is_int($type) && !($type instanceof ContentType)) {
            $type = $this->type;
        }

        if ((HtmlType::isType($type) || IframeType::isType($type)) && !Yii::$app->user->isAdmin()) {
            return false;
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function canView($user = null): bool
    {
        return $this->visibilityService->canView($user);
    }

    /**
     * Check if the field is allowed for using by the Page depending on Target and other options
     *
     * @param $field string
     * @return bool
     */
    public function isAllowedField(string $field): bool
    {
        $target = $this->getTargetModel();

        if (!$target->isAllowedField($field)) {
            return false;
        }

        $rules = $this->rules();

        foreach ($rules as $rule) {
            if (!is_array($rule) || !isset($rule[0])) {
                continue;
            }

            $firstItem = $rule[0];

            if (is_string($firstItem) && $firstItem === $field) {
                return true;
            }

            if (is_array($firstItem) && isset($firstItem[0]) && $firstItem[0] === $field) {
                return true;
            }
        }

        return false;
    }

    public function render()
    {
        return $this->getContentType()->render($this);
    }

    public function getVisibilityService(): VisibilityService
    {
        if ($this->_visibilityService === null) {
            $this->_visibilityService = new VisibilityService($this);
        }

        return $this->_visibilityService;
    }

    public function getSettingService(): SettingService
    {
        if ($this->_settingService === null) {
            $this->_settingService = new SettingService($this);
        }

        return $this->_settingService;
    }
}
