<?php

namespace humhub\modules\custom_pages\modules\template\models;

use Yii;
use humhub\modules\custom_pages\modules\template\widgets\TemplateContentFormFields;
use humhub\modules\file\models\File;

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
        $result[] = [['alt', 'file_guid'], 'safe'];
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
    
    public function beforeSave($insert)
    {
        return parent::beforeSave($insert);
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
        if($this->hasFile()) {
            $options['htmlOptions'] = [
                'src' => $this->getFile()->getUrl(),
                'alt' => $this->purify($this->alt)
            ];

            if($this->hasDefinition()) {
                $options['htmlOptions']['height'] = $this->purify($this->definition->height);
                $options['htmlOptions']['width'] = $this->purify($this->definition->width);
                $options['htmlOptions']['style'] = $this->purify($this->definition->style);
            }

            return $this->wrap('img','', $options);
        } else if(isset($options['editMode']) && $options['editMode']) {
            $options['empty'] = true;
            return $this->renderEmpty($options);
        }
        
        return '';
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
