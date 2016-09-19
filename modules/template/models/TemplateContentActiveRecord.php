<?php

namespace humhub\modules\custom_pages\modules\template\models;

use Yii;
use humhub\components\ActiveRecord;

/**
 * This is the base class for all TemplateContent Types.
 */
abstract class TemplateContentActiveRecord extends ActiveRecord
{

    const SCENARIO_CREATE = 'create';
    const SCENARIO_EDIT = 'edit';
    const SCENARIO_EDIT_ADMIN = 'edit-admin';

    private $formName;
    private $definitionInstance;
    public $definitionPostData;
    public $definitionModel;
    public $fileList = [];

    abstract public function render($options = []);

    abstract public function renderEmpty($options = []);

    abstract public function copy();

    abstract public function getLabel();

    abstract public function renderForm($form);

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

    protected function createCopy()
    {
        $copy = Yii::createObject($this->className());
        if ($this->isDefinitionContent()) {
            $copy->definition_id = $this->definition_id;
        }
        return $copy;
    }

    public function scenarios()
    {
        return [
            self::SCENARIO_DEFAULT => ['fileList', 'definitionPostData'],
            self::SCENARIO_CREATE => ['fileList', 'definitionPostData'],
            self::SCENARIO_EDIT_ADMIN => ['fileList', 'definitionPostData'],
            self::SCENARIO_EDIT => ['fileList', 'definitionPostData'],
        ];
    }

    public function load($data, $formName = null)
    {

        parent::load($data, $formName);
        if ($this->isDefinitionContent() && $this->definitionPostData != null) {
            $this->definition->load(['content' => $this->definitionPostData], 'content');
        }
    }

    public function setFormName($formName)
    {
        $this->formName = $formName;
        if ($this->definition != null) {
            $this->definition->setFormName($formName . '[definitionPostData]');
        }
    }

    public function formName()
    {
        return ($this->formName != null) ? $this->formName : parent::formName();
    }

    public function getDefinition()
    {
        if ($this->definitionModel == null) {
            return;
        }

        if ($this->definitionInstance != null) {
            return $this->definitionInstance;
        }

        $this->definitionInstance = call_user_func($this->definitionModel . "::findOne", ['id' => $this->definition_id]);

        if ($this->definitionInstance == null) {
            $this->definitionInstance = Yii::createObject($this->definitionModel);
        }

        if ($this->scenario === self::SCENARIO_EDIT_ADMIN || $this->scenario === self::SCENARIO_CREATE) {
            $this->definitionInstance->is_default = true;
        }

        $this->definitionInstance->setFormName($this->formName() . '[definitionPostData]');

        return $this->definitionInstance;
    }

    public function hasDefinition()
    {
        return isset($this->definition_id);
    }

    public function isDefinitionContent()
    {
        return $this->definitionModel != null;
    }

    public function beforeSave($insert)
    {
        $definition = $this->definition;
        if ($this->isDefinitionContent() && $definition->validate() && $definition->hasValues()) {
            $definition->save(false);
            $this->definition_id = $definition->getPrimaryKey();
        } else if ($this->isDefinitionContent() && !$definition->isNewRecord && !$definition->hasValues() && $this->scenario === self::SCENARIO_EDIT_ADMIN) {
            // If we reset the default definition to an empty state we remove the definition settings, which will allow content to define own definitions
            self::updateAll(['definition_id' => null], ['definition_id' => $definition->id]);
            $definition->delete();
            return false;
        }

        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        $this->saveFiles();
    }

    public function saveFiles()
    {
        if ($this->isNewRecord || $this->fileList == null) {
            return;
        }

        \humhub\modules\file\models\File::attachPrecreated($this, implode(',', $this->fileList));
    }

    public function afterDelete()
    {
        if ($this instanceof ContainerContent) {
            if (self::find()->where(['definition_id' => $this->definition_id])->count() == 0) {
                $this->definition->delete();
            }
        }

        $files = \humhub\modules\file\models\File::getFilesOfObject($this);

        foreach ($files as $file) {
            $file->delete();
        }

        parent::afterDelete();
    }

    protected function wrap($type, $content, $options = [], $attributes = [])
    {
        if ($this->getOption($options, 'editMode', false)) {
            $attributes['data-template-element'] = $this->getOption($options, 'element_name');
            $attributes['data-template-owner'] = $this->getOption($options, 'owner_model');
            $attributes['data-template-owner-id'] = $this->getOption($options, 'owner_id');
            $attributes['data-template-id'] = $this->getOption($options, 'template_id');
            $attributes['data-template-owner-content-id'] = $this->getOption($options, 'owner_content_id');
            $attributes['data-template-element'] = $this->getOption($options, 'element_name');
            $attributes['data-template-default'] = $this->getOption($options, 'default', '0');
            $attributes['data-template-empty'] = $this->getOption($options, 'empty', '0');
            $attributes['data-template-content'] = $this->className();


            // Note that the rendered contentid in the frontend can be the contentid of the default content if use_default is set to true
            if ($this->getPrimaryKey() != null) {
                $attributes['data-template-content-id'] = $this->getPrimaryKey();
            }
        }

        if (isset($options['htmlOptions'])) {
            $attributes = array_merge($attributes, $options['htmlOptions']);
        }

        $result = '<' . $type;
        foreach ($attributes as $key => $value) {
            if ($value != null) {
                $result .= ' ' . $key . '="' . $value . '"';
            }
        }

        return $result . '>' . $content . '</' . $type . '>';
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

    public function isEditMode($options = [])
    {
        return isset($options['editMode']) && $options['editMode'];
    }

    public function purify($content)
    {
        $config = \HTMLPurifier_Config::createDefault();
        $config->set('HTML.Attr.Name.UseCDATA', true);
     
        return \yii\helpers\HtmlPurifier::process($content,$config);
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

}
