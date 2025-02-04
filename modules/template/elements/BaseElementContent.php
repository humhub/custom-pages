<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\elements;

use humhub\interfaces\ViewableInterface;
use humhub\modules\content\components\ContentActiveRecord;
use humhub\modules\custom_pages\models\CustomPage;
use humhub\modules\custom_pages\modules\template\components\ActiveRecordDynamicAttributes;
use humhub\modules\custom_pages\modules\template\helpers\PagePermissionHelper;
use humhub\modules\custom_pages\modules\template\models\TemplateElement;
use humhub\modules\custom_pages\modules\template\models\TemplateInstance;
use humhub\modules\custom_pages\modules\template\widgets\TemplateEditorElement;
use humhub\modules\custom_pages\permissions\ManagePages;
use humhub\modules\user\components\PermissionManager;
use Yii;
use yii\db\ActiveQuery;

/**
 * This is the base class for all Template Element Content types.
 *
 * @property int $id
 * @property int $element_id
 * @property int|null $template_instance_id
 *
 * @property-read TemplateElement $element
 * @property-read BaseElementDefinition $definition
 * @property-read TemplateInstance|null $templateInstance
 */
abstract class BaseElementContent extends ActiveRecordDynamicAttributes implements ViewableInterface
{
    public const SCENARIO_CREATE = 'create';
    public const SCENARIO_EDIT = 'edit';
    public const SCENARIO_EDIT_ADMIN = 'edit-admin';

    /**
     * @var string the form name of this model used for loading form data.
     */
    private $formName;

    /**
     * @var BaseElementDefinition|null instance of the definition
     */
    private $definitionInstance;

    /**
     * @var array post data used for loading the definition instance
     * @see self::load()
     */
    public $definitionPostData;

    /**
     * @var string definition model class used by content types with definition
     */
    public $definitionModel;

    /**
     * @var array attached files used when creating/saving the record
     */
    public $fileList = [];

    /**
     * @var bool prevents multiple attach files calls
     */
    public $filesSaved = false;

    /**
     * @return string rendered content type by means of the given $options.
     */
    abstract public function render($options = []);

    /**
     * @return string empty block representation of this content type.
     */
    abstract public function renderEmpty($options = []);

    /**
     * @return string the label of this content type
     */
    abstract public function getLabel(): string;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'custom_pages_template_element_content';
    }

    /**
     * @inheritdoc
     */
    public static function instantiate($row)
    {
        $element = TemplateElement::findOne(['id' => $row['element_id']]);
        return $element ? Yii::createObject($element['content_type']) : null;
    }

    /**
     * Find records related only to the current element type
     *
     * @return ActiveQuery
     */
    public static function findByType(): ActiveQuery
    {
        return parent::find()->innerJoin(
            TemplateElement::tableName(),
            self::tableName() . '.element_id = ' . TemplateElement::tableName() . '.id AND ' .
            TemplateElement::tableName() . '.content_type = :contentType',
            ['contentType' => static::class],
        );
    }

    /**
     * Copies the values of this content type instance.
     *
     * @return static instance copy.
     */
    public function copy(): static
    {
        $clone = new static();
        $clone->element_id = $this->element_id;
        $clone->dyn_attributes = $this->dyn_attributes;
        $clone->template_instance_id = $this->template_instance_id;
        return $clone;
    }

    /**
     * @return bool determines if the content instance has currently an attribute set.
     */
    public function hasValues(): bool
    {
        foreach ($this->attributes() as $key) {
            if ($this->getAttribute($key) != null && $key != 'id') {
                return true;
            }
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $attributes = array_merge(
            ['fileList', 'definitionPostData'],
            array_keys($this->getDynamicAttributes()),
        );

        return [
            self::SCENARIO_DEFAULT => $attributes,
            self::SCENARIO_CREATE => $attributes,
            self::SCENARIO_EDIT_ADMIN => $attributes,
            self::SCENARIO_EDIT => $attributes,
        ];
    }

    /**
     * @inheritdoc
     */
    public function load($data, $formName = null)
    {
        $result = parent::load($data, $formName);
        if ($this->isDefinitionContent() && $this->definitionPostData != null) {
            $result = $this->definition->load(['content' => $this->definitionPostData], 'content') && $result;
        }
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function validate($attributeNames = null, $clearErrors = true)
    {
        if (!parent::validate($attributeNames, $clearErrors)) {
            return false;
        }

        if ($this->isDefinitionContent()) {
            return $this->definition->validate($attributeNames, $clearErrors);
        }

        return true;
    }

    /**
     * Sets the $formName of this instance and of the $definition instance if given.
     * @param string $formName
     */
    public function setFormName($formName)
    {
        $this->formName = $formName;
        if ($this->definition != null) {
            $this->definition->setFormName($formName . '[definitionPostData]');
        }
    }

    /**
     * Returns the formName for this content instance.
     * @return type
     */
    public function formName()
    {
        return ($this->formName != null) ? $this->formName : parent::formName();
    }

    /**
     * Returns the BaseTemplateElementDefinition instance of this instance. +
     * This function will create an empty definition instance if this content type has an definitionModel
     * but the definition record is not stored yet.
     *
     * @return BaseElementDefinition|null the definition instance.
     */
    public function getDefinition(): ?BaseElementDefinition
    {
        if (!$this->isDefinitionContent()) {
            return null;
        }

        if ($this->definitionInstance instanceof BaseElementDefinition) {
            if ($this->definitionInstance->isNewRecord && $this->element_id !== null) {
                // Refresh instance to the recently stored definition/element record
                $dynAttributes = $this->definitionInstance->dyn_attributes;
                $this->definitionInstance = call_user_func($this->definitionModel . "::findOne", ['id' => $this->element_id]);
                $this->definitionInstance->dyn_attributes = $dynAttributes;
            }
            return $this->definitionInstance;
        }

        if ($this->element_id !== null) {
            $this->definitionInstance = call_user_func($this->definitionModel . "::findOne", ['id' => $this->element_id]);
        }

        if ($this->definitionInstance === null) {
            // Create empty definition instance
            $this->definitionInstance = Yii::createObject($this->definitionModel);
        }

        $this->definitionInstance->setFormName($this->formName() . '[definitionPostData]');

        return $this->definitionInstance;
    }

    /**
     * @return bool determines if this content type has a definition type.
     */
    public function isDefinitionContent(): bool
    {
        return $this->definitionModel !== null;
    }

    /**
     * @ineritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if ($this->isDefinitionContent() && $this->isDefault() && $this->definition->hasValues()) {
            $this->definition->save();
        }

        if (!$this->filesSaved) {
            $this->filesSaved = true;
            $this->saveFiles();
        }
    }

    /**
     * Saves all attached files.
     */
    public function saveFiles()
    {
        if ($this->isNewRecord || $this->fileList == null) {
            return;
        }

        $this->fileManager->attach($this->fileList);
    }

    /**
     * @ineritdoc
     */
    public function afterDelete()
    {
        $files = $this->fileManager->findAll();

        foreach ($files as $file) {
            $file->delete();
        }

        parent::afterDelete();
    }

    protected function wrap($type, $content, $options = [], $attributes = [])
    {
        if ($this->getPrimaryKey() != null) {
            $options['element_content_id'] = $this->getPrimaryKey();
        }

        return TemplateEditorElement::widget([
            'container' => $type,
            'elementContent' => $this,
            'content' => $content,
            'renderOptions' => $options,
            'renderAttributes' => $attributes,
        ]);
    }

    public function isEditMode($options = [])
    {
        return isset($options['editMode']) && $options['editMode'];
    }

    public function purify($content)
    {
        $config = \HTMLPurifier_Config::createDefault();
        $config->set('HTML.Attr.Name.UseCDATA', true);
        $config->set('Attr.AllowedFrameTargets', ['_blank']);

        return \yii\helpers\HtmlPurifier::process($content, $config);
    }

    protected function renderEmptyDiv($title, $options = [], $attributes = [])
    {
        if ($this->isEditMode($options)) {
            $class = $this->getOption($options, 'class', 'emptyBlock');
            $defaultContent = '<div class="' . $class . '"><strong>' . $title . '</strong></div>';
            return $this->wrap('div', $defaultContent, $options, $attributes);
        }
        return '';
    }

    public function getOption($options, $key, $default = null)
    {
        if (isset($options[$key])) {
            if (is_bool($options[$key])) {
                return ($options[$key]) ? '1' : '0';
            } else {
                return $options[$key];
            }
        } else {
            return $default;
        }
        return isset($options[$key]) ? strval($options[$key]) : $default;
    }

    /**
     * Check if the Element is empty
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->isNewRecord;
    }

    public function getElement(): ActiveQuery
    {
        return $this->hasOne(TemplateElement::class, ['id' => 'element_id']);
    }

    public function getTemplateInstance(): ActiveQuery
    {
        return $this->hasOne(TemplateInstance::class, ['id' => 'template_instance_id']);
    }

    public function getPage(): ?CustomPage
    {
        return $this->templateInstance?->page;
    }

    /**
     * @inheritdoc
     */
    public function canView($user = null): bool
    {
        $page = $this->getPage();

        if ($page instanceof ViewableInterface) {
            return $page->canView($user);
        }

        if ($page instanceof ContentActiveRecord) {
            return $page->content->canView($user);
        }

        if ($this->template_instance_id === null) {
            // If this template content record is not linked to any container(Page, Snippet),
            // then it is from Template Layout, try to check if the user can manage Template Layouts
            return (new PermissionManager(['subject' => $user]))->can(ManagePages::class);
        }

        return false;
    }

    /**
     * @param null $user
     * @return bool
     */
    public function canEdit($user = null): bool
    {
        return PagePermissionHelper::canEdit();
    }

    /**
     * @return bool False - if the content has a dynamic content, and it must not be cached
     */
    public function isCacheable(): bool
    {
        return true;
    }

    /**
     * Get a view file name to render a form with fields for this Element Content
     *
     * @return string
     */
    public function getFormView(): string
    {
        return lcfirst(substr(strrchr(static::class, '\\'), 1, -7));
    }

    public function isDefault(): bool
    {
        return $this->template_instance_id === null;
    }

    public function getInstance(bool $createDummy = false): ?static
    {
        if ($this->isNewRecord && $createDummy) {
            /* @var static $content */
            $content = Yii::createObject($this->element->content_type);
            $content->element_id = $this->element_id;
            return $content;
        }

        return $this;
    }
}
