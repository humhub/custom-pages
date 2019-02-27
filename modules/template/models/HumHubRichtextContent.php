<?php

namespace humhub\modules\custom_pages\modules\template\models;

use humhub\modules\content\widgets\richtext\RichText;
use Yii;
use humhub\modules\custom_pages\modules\template\widgets\TemplateContentFormFields;

/**
 * Class HumHubRichtextContent
 *
 * @property string $content
 */
 class HumHubRichtextContent extends TemplateContentActiveRecord
{
    public static $label = 'HumHub Richtext';
     
    /**
     * @return string the associated database table name
     */
    public static function tableName()
    {
        return 'custom_pages_template_hh_richtext_content';
    }
    
    public function rules()
    {
        $result = parent::rules();
        $result[] = ['content', 'required'];
        return $result;
    }
    
        
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CREATE][] = 'content';
        $scenarios[self::SCENARIO_EDIT_ADMIN][] = 'content';
        $scenarios[self::SCENARIO_EDIT][] = 'content';
        return $scenarios;
    }
    
    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return  [
            'content' => 'Content',
        ];
    }
    
    public function getLabel()
    {
        return self::$label;
    }
    
    public function copy() {
        return new HumHubRichtextContent(['content' => $this->content]);;
    }

    public function render($options = [])
    {   
        if($this->isEditMode($options)) {
            return $this->wrap('div', Richtext::output($this->content), $options);
        } 
        
        return Richtext::output($this->content);
    }

     public function saveFiles()
     {
         Richtext::postProcess($this->content, $this);
     }
    
    public function renderEmpty($options = [])
    {
        return $this->renderEmptyDiv(Yii::t('CustomPagesModule.models_RichtextContent', 'Empty HumHub Richtext'), $options);
    }

    public function renderForm($form)
    {
        return TemplateContentFormFields::widget([
            'type' => 'humhub_richtext',
            'form' => $form,
            'model' => $this
        ]);
    }

}
