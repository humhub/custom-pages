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
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        $result = parent::rules();
        $result[] = [['file_guid'], 'required'];
        $result[] = [['alt', 'file_guid'], 'safe'];
        return $result;
    }
       
    /**
     * @inheritdoc
     */
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
            'file_guid' => Yii::t('CustomPagesModule.base', 'File')
        ];
    }
    
    public function saveFiles()
    {
        $files = File::findByRecord($this);

        foreach($files as $file) {
            if($file->guid !== $this->file_guid) {
                $file->delete();
            }
        }
        
        $this->fileManager->attach($this->file_guid);
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
