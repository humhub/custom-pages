<?php

namespace humhub\modules\custom_pages\modules\template\models;

use Yii;
use humhub\modules\custom_pages\modules\template\widgets\TemplateContentFormFields;

 class ImageContent extends FileContent
{
    public static $label = 'Image';
    
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
        $result = [];
        // We preven the content instance from beeing saved if there is no definition setting, to get sure we have an empty content in this case
        // TODO: perhaps overwrite the validate method and call parent validate only if no definition is set
        if($this->definition == null || !$this->definition->hasValues()) {
            $result[] = [['file_guid'], 'required'];
        }
        $result[] = [['alt', 'file_guid'], 'safe'];
        return $result;
    }
        
    public function scenarios()
    {
        $scenarios = parent::scenarios();
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
            'file_guid' =>  Yii::t('CustomPagesModule.base', 'File'),
            'alt' =>  Yii::t('CustomPagesModule.base', 'Alternate text')
        ];
    }
    
    public function copy() {
        $clone = parent::copy();
        $clone->alt = $this->alt;
        return $clone;
    }

    public function render($options = [])
    {   
        if($this->hasFile() != null) {
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
