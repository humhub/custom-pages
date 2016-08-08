<?php

namespace humhub\modules\custom_pages\modules\template\models;

use Yii;
use humhub\modules\custom_pages\modules\template\widgets\TemplateContentFormFields;
use humhub\modules\file\models\File;


/**
 * This is the model class for table "custom_pages_page".
 *
 * The followings are the available columns in table 'custom_pages_page':
 */
 class ImageContent extends TemplateContentActiveRecord
{
    public static $label = 'Image';
    
    public $file;
    
    public function init()
    {
        $this->definitionModel = ImageContentDefinition::className();
    }
     
    /**
     * @return string the associated database table name
     */
    public static function tableName()
    {
        return 'custom_pages_template_image_content';
    }
    
    public function rules()
    {
        $result = parent::rules();
        $result[] = ['alt', 'safe'];
        $result[] = ['file_guid', 'required'];
        return $result;
    }
    
        
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CREATE][] = 'file_guid';
        $scenarios[self::SCENARIO_EDIT_ADMIN][] = 'file_guid';
        $scenarios[self::SCENARIO_EDIT][] = 'file_guid';
        $scenarios[self::SCENARIO_CREATE][] = 'alt';
        $scenarios[self::SCENARIO_EDIT_ADMIN][] = 'alt';
        $scenarios[self::SCENARIO_EDIT][] = 'alt';
        return $scenarios;
    }
    
    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return  [
            'file_guid' => 'File',
            'alt' => 'Alternate text'
        ];
    }
    
    public function getLabel()
    {
        return self::$label;
    }
    
    public function getFile()
    {
        return File::findOne(['guid' => $this->file_guid]);
    }
    
    public function hasFile()
    {
        return $this->file_guid != null;
    }
    
    public function getUrl()
    {
        return $this->getFile()->getUrl();
    }
    
    public function copy() {
        $clone = $this->createCopy();
        $clone->file_guid = $this->file_guid;
        $clone->alt = $this->alt;
        return $clone;
    }

    public function render($options = [])
    {   
        
        $options['htmlOptions'] = [
            'src' => $this->getFile()->getUrl(),
            'alt' => $this->alt
        ];
        
        if($this->hasDefinition()) {
            $options['htmlOptions']['height'] = $this->definition->height;
            $options['htmlOptions']['width'] = $this->definition->width;
            $options['htmlOptions']['style'] = $this->definition->style;
        }
        
        return $this->wrap('img','', $options);
    }
    
    public function renderEmpty($options = [])
    {
        return $this->renderEmptyDiv(Yii::t('CustomPagesModule.models_ImageContent', 'Empty Image'), $options);
    }

    public function renderForm($form)
    {
        return TemplateContentFormFields::widget([
            'type' => 'image',
            'form' => $form,
            'model' => $this
        ]);
    }

}
