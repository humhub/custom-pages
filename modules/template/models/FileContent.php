<?php

namespace humhub\modules\custom_pages\modules\template\models;

use Yii;
use humhub\modules\custom_pages\modules\template\widgets\TemplateContentFormFields;
use humhub\modules\file\models\File;

 class FileContent extends TemplateContentActiveRecord
{
    public static $label = 'File';
    
    public $file;
     
    /**
     * @return string the associated database table name
     */
    public static function tableName()
    {
        return 'custom_pages_template_file_content';
    }
    
    public function rules()
    {
        
        $result = parent::rules();
        $result[] = [['file_guid'], 'required'];
        $result[] = [['alt', 'file_guid'], 'safe'];
        return $result;
    }
    
        
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CREATE][] = 'file_guid';
        $scenarios[self::SCENARIO_EDIT_ADMIN][] = 'file_guid';
        $scenarios[self::SCENARIO_EDIT][] = 'file_guid';
        return $scenarios;
    }
    
    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return  [
            'file_guid' => 'File',
        ];
    }
    
    public function getLabel()
    {
        return static::$label;
    }
    
    public function getFile()
    {
        return File::findOne(['guid' => $this->file_guid]);
    }
    
    public function hasFile()
    {
        return $this->file_guid != null && $this->getFile() != null;
    }
    
    public function getUrl()
    {
        $file = $this->getFile();
        return ($file != null) ? $file->getUrl() : null;
    }
    
    public function copy() {
        $clone = $this->createCopy();
        $clone->file_guid = $this->file_guid;
        return $clone;
    }

    public function render($options = [])
    {   
        if($this->hasFile()) {
            return $this->getFile()->getUrl();
        }
        return '';
    }
    
    public function renderEmpty($options = [])
    {
        return '';
    }

    public function renderForm($form)
    {
        return TemplateContentFormFields::widget([
            'type' => 'file',
            'form' => $form,
            'model' => $this
        ]);
    }

}
