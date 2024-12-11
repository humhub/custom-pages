<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\models;

use humhub\interfaces\ViewableInterface;
use humhub\modules\admin\permissions\ManageModules;
use humhub\modules\content\components\ContentActiveRecord;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\content\models\Content;
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
use humhub\modules\custom_pages\types\ContentType;
use humhub\modules\custom_pages\types\HtmlType;
use humhub\modules\custom_pages\types\IframeType;
use humhub\modules\custom_pages\types\LinkType;
use humhub\modules\custom_pages\types\MarkdownType;
use humhub\modules\custom_pages\types\PhpType;
use humhub\modules\custom_pages\types\TemplateType;
use humhub\modules\custom_pages\widgets\WallEntry;
use humhub\modules\space\models\Space;
use humhub\modules\user\helpers\AuthHelper;
use humhub\modules\user\models\User;
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
 * @property bool $admin_only
 * @property bool $in_new_window
 * @property string $cssClass
 * @property string $url
 * @property string $abstract
 */
class CustomPage extends ContentActiveRecord implements ViewableInterface
{
    use PhpPageContainer;
    use TemplatePageContainer;

    public const VISIBILITY_ADMIN_ONLY = 3;
    public const VISIBILITY_PRIVATE = 0;
    public const VISIBILITY_PUBLIC = 1;

    /**
     * @inheritdoc
     */
    public $silentContentCreation = true;

    /**
     * @var Target cached target
     */
    private $_target;

    /**
     * @var int special field for template based pages specifying the layout template id
     */
    public $templateId;

    /**
     * @var bool field only used in edit form
     */
    public $visibility;

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
            'admin_only' => Yii::t('CustomPagesModule.model', 'Only visible for admins'),
            'in_new_window' => Yii::t('CustomPagesModule.model', 'Open in new window'),
            'cssClass' => Yii::t('CustomPagesModule.base', 'Style Class'),
            'url' => Yii::t('CustomPagesModule.model', 'Url shortcut'),
            'abstract' => Yii::t('CustomPagesModule.model', 'Abstract'),
            'content' => $this->getContentType() ? $this->getContentType()->getLabel() : null,
            'targetUrl' => Yii::t('CustomPagesModule.base', 'Target Url'),
            'templateId' => Yii::t('CustomPagesModule.base', 'Template Layout'),
            'visibility' => Yii::t('CustomPagesModule.model', 'Visibility'),
        ];

        if ($this->isSnippet()) {
            // Any(global + space) Snippet
            $result['page_content'] = Yii::t('CustomPagesModule.model', 'Content');
        } elseif (!$this->isGlobal()) {
            // Only space Page
            $result['page_content'] = PhpType::isType($this->getContentType())
                ? Yii::t('CustomPagesModule.model', 'View')
                : Yii::t('CustomPagesModule.base', 'Content');
            $result['admin_only'] = Yii::t('CustomPagesModule.model', 'Only visible for space admins');
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = [
            [['type', 'title', 'target'], 'required'],
            [['type'], 'integer'],
            [['target'], 'validateTarget'],
            [['type'], 'validateContentType'],
            [['visibility'], 'integer', 'min' => self::VISIBILITY_PRIVATE, 'max' => self::VISIBILITY_ADMIN_ONLY],
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

        if ($target->isAllowedField('admin_only')) {
            $rules[] = [['admin_only'], 'integer'];
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

        if ($this->admin_only) {
            $this->visibility = static::VISIBILITY_ADMIN_ONLY;
        } elseif ($this->content->isPublic()) {
            $this->visibility = static::VISIBILITY_PUBLIC;
        } else {
            $this->visibility = static::VISIBILITY_PRIVATE;
        }
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        $this->fixVisibility();

        switch ($this->visibility) {
            case static::VISIBILITY_ADMIN_ONLY:
                $this->admin_only = 1;
                $this->content->visibility = Content::VISIBILITY_PRIVATE;
                break;
            case static::VISIBILITY_PUBLIC:
                $this->admin_only = 0;
                $this->content->visibility = Content::VISIBILITY_PUBLIC;
                break;
            default:
                $this->admin_only = 0;
                $this->content->visibility = Content::VISIBILITY_PRIVATE;
                break;
        }

        // Keep page hidden on stream when "Abstract" field is not filled, or it is visible only for admin
        $this->content->hidden = $this->admin_only || !$this->checkAbstract();

        return parent::beforeSave($insert);
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if (!$this->getContentType()->afterSave($this, $insert, $changedAttributes)) {
            throw new LogicException('Could not save content type' . $this->getContentType()->getLabel());
        }

        if ($this->checkAbstract()) {
            RichText::postProcess($this->abstract, $this);
        }
    }

    /**
     * @inheritdoc
     */
    public function afterDelete()
    {
        $this->getContentType()->afterDelete($this);
        parent::afterDelete();
    }

    /**
     * Fix visibility to proper value if current cannot be used depending on other attributes
     */
    public function fixVisibility(): void
    {
        // Force visibility access "Members & Guests" to "Members only" for
        // page type "User Account Menu (Settings)"
        if ($this->getTargetId() == PageType::TARGET_ACCOUNT_MENU) {
            if ($this->visibility == self::VISIBILITY_PUBLIC) {
                $this->visibility = self::VISIBILITY_PRIVATE;
            }
            if ($this->content->visibility == Content::VISIBILITY_PUBLIC) {
                $this->content->visibility = Content::VISIBILITY_PRIVATE;
            }
        }
    }

    protected function checkAbstract(): bool
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
        return $this->title;
    }

    /**
     * Returns all allowed content types for a page container class.
     *
     * @return ContentType|null
     */
    public function getContentType(): ?ContentType
    {
        return ContentType::getById($this->type);
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
        return Template::getSelection([
            'type' => $this->isSnippet() ? Template::TYPE_SNIPPED_LAYOUT : Template::TYPE_LAYOUT,
            'allow_for_spaces' => $this->isGlobal() ? 0 : 1,
        ]);
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

    public function hasTarget($targetId): bool
    {
        return $this->target === $targetId;
    }

    public function getTemplateId()
    {
        if (!$this->templateId) {
            $templateInstance = TemplateInstance::findByOwner($this);
            if ($templateInstance) {
                $this->templateId = $templateInstance->template_id;
            }
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

    public function getVisibilitySelection(): array
    {
        $result = [
            static::VISIBILITY_ADMIN_ONLY => Yii::t('CustomPagesModule.base', 'Admin only'),
        ];

        if ($this->isGlobal()) {
            if (AuthHelper::isGuestAccessEnabled()) {
                $result[static::VISIBILITY_PRIVATE] = Yii::t('CustomPagesModule.base', 'Members only');
                if ($this->getTargetId() != PageType::TARGET_ACCOUNT_MENU) {
                    $result[static::VISIBILITY_PUBLIC] = Yii::t('CustomPagesModule.base', 'Members & Guests');
                }
            } else {
                $result[static::VISIBILITY_PUBLIC] = Yii::t('CustomPagesModule.base', 'All Members');
            }
        } else {
            $result[static::VISIBILITY_PRIVATE] = Yii::t('CustomPagesModule.base', 'Space Members only');

            if ($this->content->container->visibility != Space::VISIBILITY_NONE) {
                $result[static::VISIBILITY_PUBLIC] = Yii::t('CustomPagesModule.base', 'Public');
            }
        }

        return $result;
    }

    public function canEdit($type = null): bool
    {
        if (!($this->content->container instanceof Space && $this->content->container->isAdmin()) &&
            !Yii::$app->user->can(ManagePages::class)) {
            return false;
        }

        if (!($type instanceof ContentType)) {
            $type = $this->type;
        }

        if (HtmlType::isType($type) && !Yii::$app->user->isAdmin()) {
            return false;
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function canView($user = null): bool
    {
        if ($this->admin_only && !self::canSeeAdminOnlyContent($this->content->container)) {
            return false;
        }

        // Todo: Workaround for bug present prior to HumHub v1.3.18
        if (Yii::$app->user->isGuest && !$this->content->container && $this->content->isPublic()) {
            return true;
        }

        // Todo: Workaround for global content visibility bug present prior to HumHub v1.5
        if (empty($this->content->contentcontainer_id) && !Yii::$app->user->isGuest) {
            return true;
        }

        return $this->content->canView($user);
    }

    public static function canSeeAdminOnlyContent(ContentContainerActiveRecord $container = null)
    {
        if (Yii::$app->user->isGuest) {
            return false;
        }

        if (!$container) {
            return Yii::$app->user->isAdmin() || Yii::$app->user->can([ManageModules::class, ManagePages::class]);
        }

        if ($container instanceof Space) {
            return $container->isAdmin();
        }

        if ($container instanceof User) {
            $container->is(Yii::$app->user->getIdentity());
        }

        return false;
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
}
