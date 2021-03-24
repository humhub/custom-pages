<?php

namespace humhub\modules\custom_pages\models;

use humhub\modules\admin\permissions\ManageModules;
use humhub\modules\content\components\ContentActiveRecord;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\content\models\Content;
use humhub\modules\content\widgets\richtext\RichText;
use humhub\modules\custom_pages\components\PhpPageContainer;
use humhub\modules\custom_pages\components\TemplatePageContainer;
use humhub\modules\custom_pages\interfaces\CustomPagesService;
use humhub\modules\custom_pages\modules\template\models\TemplateInstance;
use humhub\modules\custom_pages\permissions\ManagePages;
use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;
use LogicException;
use Yii;

/**
 * This abstract class is used by all custom content container types as pages and snippets.
 *
 * Note: Subclasses may container global types not bound to any ContentContainerActiveRecord
 *
 * The followings are the available columns in table 'custom_pages_page':
 * @property integer $id
 * @property integer $type
 * @property string $title
 * @property string $icon
 * @property string $page_content
 * @property integer $sort_order
 * @property integer $admin_only
 * @property integer $in_new_window
 * @property string $target
 * @property string $cssClass
 * @property string $url
 */
abstract class CustomContentContainer extends ContentActiveRecord
{
    use PhpPageContainer;
    use TemplatePageContainer;

    const VISIBILITY_ADMIN_ONLY = 3;
    const VISIBILITY_PRIVATE = 0;
    const VISIBILITY_PUBLIC = 1;

    /**
     * @inheritdoc
     */
    public $streamChannel = null;


    /**
     * @var Target cached target
     */
    private $_target;

    /**
     * @var integer special field for template based pages specifying the layout template id
     */
    public $templateId;

    /**
     * @var bool field only used in edit form
     */
    public $visibility;

    public function afterFind()
    {
        parent::afterFind();

        if($this->admin_only) {
            $this->visibility = static::VISIBILITY_ADMIN_ONLY;
        } else if($this->content->isPublic()) {
            $this->visibility = static::VISIBILITY_PUBLIC;
        } else {
            $this->visibility = static::VISIBILITY_PRIVATE;
        }
    }

    /**
     * Returns the database content field. Note this does not render the any content.
     */
    public abstract function getPageContent();

    /**
     * Returns the database content field. Note this does not render the any content.
     */
    public function getPageContentProperty() {
        return 'page_content';
    }

    /**
     * Returns all allowed templates for a page container class.
     */
    public abstract function getAllowedTemplateSelection();

    /**
     * Returns the page container class label.
     * @return string
     */
    public abstract function getLabel();

    /**
     * Returns the view file path for PHP based content.
     * @return string
     */
    public abstract function getPhpViewPath();

    /**
     * @return string
     */
    public abstract function getEditUrl();

    /**
     * @return string
     */
    public abstract function getPageType();

    /**
     * @return array
     */
    public function getVisibilitySelection() {
        $result = [
            static::VISIBILITY_ADMIN_ONLY => Yii::t('CustomPagesModule.visibility', 'Admin only')
        ];

        if($this->isGuestAccessEnabled()) {
            $result[static::VISIBILITY_PRIVATE] = Yii::t('CustomPagesModule.visibility', 'Members only');
            if ($this->getTargetId() != Page::NAV_CLASS_ACCOUNTNAV) {
                $result[static::VISIBILITY_PUBLIC] = Yii::t('CustomPagesModule.visibility', 'Members & Guests');
            }
        } else {
            $result[static::VISIBILITY_PUBLIC] = Yii::t('CustomPagesModule.visibility', 'All Members');
        }

        return $result;
    }

    /**
     * Helper function can be replaced with AuthHelper::isGuestAccessEnabled() after min-version 1.4
     *
     * @return boolean
     */
    protected function isGuestAccessEnabled()
    {
        /** @var Module $module */
        $module = Yii::$app->getModule('user');

        if ($module->settings->get('auth.allowGuestAccess')) {
            return true;
        }

        return false;
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function defaultAttributeLabels()
    {
        return [
            'id' => Yii::t('CustomPagesModule.components_Container', 'ID'),
            'type' => Yii::t('CustomPagesModule.components_Container', 'Type'),
            'title' => Yii::t('CustomPagesModule.components_Container', 'Title'),
            'icon' => Yii::t('CustomPagesModule.components_Container', 'Icon'),
            'cssClass' => Yii::t('CustomPagesModule.components_Container', 'Style Class'),
            'content' => $this->getContentType() ?  $this->getContentType()->getLabel() : null ,
            'sort_order' => Yii::t('CustomPagesModule.components_Container', 'Sort Order'),
            'targetUrl' => Yii::t('CustomPagesModule.components_Container', 'Target Url'),
            'templateId' => Yii::t('CustomPagesModule.components_Container', 'Template Layout'),
            'admin_only' => Yii::t('CustomPagesModule.models_Page', 'Only visible for admins')
        ];
    }

    /**
     * Returns the default validation rules of a container, this may be overwritten or extended by subclasses.
     *
     * @return array
     */
    public function defaultRules()
    {
        $result = [
            [['type', 'title', 'target'], 'required'],
            [['type'], 'integer'],
            ['target', 'validateTarget'],
            [['type'], 'validateContentType'],
            [['visibility'], 'integer', 'min' => static::VISIBILITY_PRIVATE, 'max' => static::VISIBILITY_ADMIN_ONLY],
            [['title', 'target'], 'string', 'max' => 255],
        ];

        $result = array_merge($result, $this->getRulesByTarget());
        return array_merge($result, $this->getRulesByContentType());
    }

    public function validateTarget()
    {
        $target = $this->getTargetModel();
        if(!$target) {
            $this->addError('target', 'Target not available for this page container.');
        }
    }

    public function validateContentType()
    {
        $target = $this->getTargetModel();
        if($target && !$target->isAllowedContentType($this->type)) {
            $this->addError('target', 'The selected content type is not allowed for this target.');
        }
    }

    /**
     * @inheritdoc
     */
    public function getContentName()
    {
        $target = $this->getTargetModel();
        if($target && $target->contentName) {
            return $target->contentName;
        }

        return PageType::getContentName($this->getPageType());
    }

    private function getRulesByContentType()
    {
        $result = [];
        $type = $this->getContentType();

        if(!$type) {
            return $result;
        }

        if(PhpType::isType($type) || LinkType::isType($type) || HtmlType::isType($type) || MarkdownType::isType($type) || IframeType::isType($type)) {
            $result[] = ['page_content', 'required'];
        }

        if(PhpType::isType($type)) {
            $result[] = ['type', 'validatePhpType'];
        }

        if(TemplateType::isType($type)) {
            $result[] = [['templateId'], 'safe'];
            $result[] = ['type', 'validateTemplateType'];
        }

        if($type->hasContent()) {
            $result[] =  [['page_content'], 'safe'];
        }

        return $result;
    }

    private function getRulesByTarget()
    {
        $result = [];
        $target = $this->getTargetModel();

        if (!$target) {
            return $result;
        }

        if ($target->isAllowedField('admin_only')) {
            $result[] = [['admin_only'], 'integer'];
        }

        if ($target->isAllowedField('sort_order')) {
            $result[] = [['sort_order'], 'integer'];
        }

        if ($target->isAllowedField('icon')) {
            $result[] = [['icon'], 'string', 'max' => 100];
        }

        if ($target->isAllowedField('cssClass') && !LinkType::isType($this->getContentType())) {
            $result[] = [['cssClass'], 'string', 'max' => 255];
        }

        return $result;
    }

    public function canView() {
        if($this->admin_only && !static::canSeeAdminOnlyContent($this->content->container)) {
            return false;
        }

        // Todo: Workaround for bug present prior to HumHub v1.3.18
        if(Yii::$app->user->isGuest && !$this->content->container && $this->content->isPublic()) {
            return true;
        }

        // Todo: Workaround for global content visibility bug present prior to HumHub v1.5
        if(empty($this->content->contentcontainer_id) && !Yii::$app->user->isGuest) {
            return true;
        }

        return $this->content->canView();
    }

    public static function canSeeAdminOnlyContent(ContentContainerActiveRecord $container = null)
    {
        if(Yii::$app->user->isGuest) {
            return false;
        }

        if(!$container) {
            return Yii::$app->user->isAdmin() || Yii::$app->user->can([ManageModules::class, ManagePages::class]);
        }

        if($container instanceof Space) {
            return $container->isAdmin();
        }

        if($container instanceof User) {
            $container->is(Yii::$app->user->getIdentity());
        }

        return false;
    }


    /**
     * @param $field string
     * @return bool
     */
    public function isAllowedField($field)
    {
        $target = $this->getTargetModel();

        if(!$target->isAllowedField($field)) {
            return false;
        }

        $rules = $this->rules();

        foreach ($rules as $rule) {
            if(!is_array($rule) || !isset($rule[0])) {
                continue;
            }

            $firstItem = $rule[0];

            if(is_string($firstItem) && $firstItem === $field) {
                return true;
            }

            if(is_array($firstItem) && isset($firstItem[0]) && $firstItem[0] === $field) {
                return true;
            }
        }

        return false;
    }

    public function setTemplateId($value)
    {
        return $this->templateId = $value;
    }

    public function getTemplateId()
    {
        if(!$this->templateId) {
            $templateInstance = TemplateInstance::findByOwner($this);
            if($templateInstance) {
                $this->templateId = $templateInstance->template_id;
            }
        }
        return $this->templateId;
    }

    /**
     * Returns the view url of this page.
     */
    public function getUrl()
    {
        return $this->getTargetModel()->getContentUrl($this);
    }

    /**
     * Returns all allowed content types for a page container class.
     *
     * @return ContentType
     */
    public function getContentType()
    {
        return ContentType::getById($this->type);
    }


    /**
     * @return Target
     */
    public function getTargetModel()
    {
        if(!$this->_target) {
            $this->_target = (new CustomPagesService())->getTargetById($this->getTargetId(), $this->getPageType(), $this->content->container);
        }

        return $this->_target;
    }

    public function render()
    {
        return $this->getContentType()->render($this);
    }

    public function getTargetId()
    {
        return $this->target;
    }

    public function hasTarget($targetId)
    {
        return $this->target === $targetId;
    }

    /**
     * @return string returns the title of this container
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string|null
     */
    public function getIcon() {
        if($this->hasAttribute('icon') && $this->icon) {
            return $this->icon;
        }

        $target = $this->getTargetModel();
        if($target) {
            return $target->getIcon();
        }

        return null;
    }

    /**
     * @param $insert
     * @return bool
     * @throws \yii\base\Exception
     */
    public function beforeSave($insert)
    {
        switch($this->visibility) {
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

        if($this->checkAbstract() && !$this->admin_only) {
            $this->streamChannel = 'default';
        } else {
            $this->streamChannel = null;
        }

        return parent::beforeSave($insert);
    }

    /**
     * @return bool
     */
    protected function checkAbstract()
    {
        return $this->isAllowedField('abstract')
            && $this->hasAttribute('abstract')
            && !empty($this->abstract);
    }

    /**
     * @param $insert
     * @param $changedAttributes
     * @throws \yii\base\InvalidConfigException
     */
    public function afterSave($insert, $changedAttributes)
    {
        if(!$this->getContentType()->afterSave($this, $insert, $changedAttributes)) {
            throw new LogicException('Could not save content type'.$this->getContentType()->getLabel());
        }

        if($this->checkAbstract()) {
            RichText::postProcess($this->abstract, $this);
        }

        parent::afterSave($insert, $changedAttributes);
    }

    /**
     *
     */
    public function afterDelete()
    {
        $this->getContentType()->afterDelete($this);
        parent::afterDelete();
    }

    /**
     * Fix visibility to proper value if current cannot be used depending on other attributes
     */
    public function fixVisibility()
    {
    }
}
