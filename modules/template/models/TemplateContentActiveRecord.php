<?php

namespace humhub\modules\custom_pages\modules\template\models;

use Yii;
use humhub\components\ActiveRecord;

/**
 * This is the base class for all TemplateContent types.
 */
abstract class TemplateContentActiveRecord extends ActiveRecord
{

    const SCENARIO_CREATE = 'create';
    const SCENARIO_EDIT = 'edit';
    const SCENARIO_EDIT_ADMIN = 'edit-admin';

    /**
     * @var string the formname of this model used for loading form data.
     */
    private $formName;

    /**
     * @var ContainerContentDefinition instance of this template
     */
    private $definitionInstance;

    /**
     * @var array post data used for loading the definition instance
     * @see TemplateContentActiveRecord::load()
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
     * Copies the values of this content type instance.
     * This function can initiate the copy by using `createCopy`.
     *
     * @see TemplateContentActiveRecord::createCopy()
     * @return TemplateContentActiveRecord instance copy.
     */
    abstract public function copy();

    /**
     * @return string the label of this content type
     */
    abstract public function getLabel();

    /**
     * @param \yii\widgets\ActiveForm $form form instance
     * @return string edit form of this content type
     */
    abstract public function renderForm($form);

    /**
     * @return boolean determines if the content instance has currently an attribute set.
     */
    public function hasValues()
    {
        $result = false;
        foreach ($this->attributes() as $key) {
            if ($this->getAttribute($key) != null && $key != 'id') {
                $result = true;
                break;
            }
        }
        return $result;
    }

    /**
     * Creates an empty copy of the current conten tpye and adopts the definition_id (if present).
     * @return TemplateContentActiveRecord
     */
    protected function createCopy()
    {
        $copy = Yii::createObject(get_class($this));
        if ($this->isDefinitionContent()) {
            $copy->definition_id = $this->definition_id;
        }
        return $copy;
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            self::SCENARIO_DEFAULT => ['fileList', 'definitionPostData'],
            self::SCENARIO_CREATE => ['fileList', 'definitionPostData'],
            self::SCENARIO_EDIT_ADMIN => ['fileList', 'definitionPostData'],
            self::SCENARIO_EDIT => ['fileList', 'definitionPostData'],
        ];
    }

    /**
     * Loads the given $data and also loads the definition if $definitionPostData is set.
     * @param type $data
     * @param type $formName
     */
    public function load($data, $formName = null)
    {
        $result = parent::load($data, $formName);
        if ($this->isDefinitionContent() && $this->definitionPostData != null) {
            $this->definition->load(['content' => $this->definitionPostData], 'content');
        }
        return $result;
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
     * Returns the ContainerContentDefinition instance of this instance. +
     * This function will create an empty definition instance if this content type has an definitionModel and
     * does not have an related definition_id.
     *
     * @return ContainerContentDefinition the definition instance.
     */
    public function getDefinition()
    {
        if (!$this->isDefinitionContent()) {
            return;
        }

        if ($this->definitionInstance) {
            return $this->definitionInstance;
        }

        if ($this->definition_id != null) {
            $this->definitionInstance = call_user_func($this->definitionModel . "::findOne", ['id' => $this->definition_id]);
        }

        // Create empty definition instance
        if (!$this->definitionInstance) {
            $this->definitionInstance = Yii::createObject($this->definitionModel);
        }

        // Mark the definition instance as default if the content is created or edited by admin
        if ($this->scenario === self::SCENARIO_EDIT_ADMIN || $this->scenario === self::SCENARIO_CREATE) {
            $this->definitionInstance->is_default = true;
        }

        $this->definitionInstance->setFormName($this->formName() . '[definitionPostData]');

        return $this->definitionInstance;
    }

    /**
     * @return boolean determines if this content instance has an definition instance relation.
     */
    public function hasDefinition()
    {
        return isset($this->definition_id);
    }

    /**
     * @return boolean determines if this content type has an definition type.
     */
    public function isDefinitionContent()
    {
        return $this->definitionModel != null;
    }

    /**
     * @ineritdoc
     */
    public function beforeSave($insert)
    {
        $definition = $this->definition;
        if ($this->isDefinitionContent() && $definition->validate() && $definition->hasValues()) {
            $definition->save(false);
            $this->definition_id = $definition->getPrimaryKey();
        } else if ($this->isDefinitionContent() && !$definition->isNewRecord && !$definition->hasValues() && $this->scenario === self::SCENARIO_EDIT_ADMIN) {
            // If we reset the default definition to an empty state we remove the definition settings, which will allow the content to define own definitions
            self::updateAll(['definition_id' => null], ['definition_id' => $definition->id]);
            $definition->delete();
            return false;
        }

        return parent::beforeSave($insert);
    }

    /**
     * @ineritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if(!$this->filesSaved) {
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
        if ($this instanceof ContainerContent) {
            if (self::find()->where(['definition_id' => $this->definition_id])->count() == 0) {
                $this->definition->delete();
            }
        }

        $files = $this->fileManager->findAll();

        foreach ($files as $file) {
            $file->delete();
        }

        parent::afterDelete();
    }

    protected function wrap($type, $content, $options = [], $attributes = [])
    {
        if ($this->getPrimaryKey() != null) {
            $options['template-content-id'] = $this->getPrimaryKey();
        }

        return \humhub\modules\custom_pages\modules\template\widgets\TemplateEditorElement::widget([
            'container' => $type,
            'templateContent' => $this,
            'content' => $content,
            'renderOptions' => $options,
            'renderAttributes' => $attributes
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

}
