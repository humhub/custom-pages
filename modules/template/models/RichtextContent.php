<?php

namespace humhub\modules\custom_pages\modules\template\models;

use Yii;
use humhub\modules\custom_pages\modules\template\widgets\TemplateContentFormFields;

/**
 * Class RichtextContent
 * @package humhub\modules\custom_pages\modules\template\models
 *
 * @property string $content
 */
class RichtextContent extends TemplateContentActiveRecord
{
    public static $label = 'Richtext';
     
    /**
     * @return string the associated database table name
     */
    public static function tableName()
    {
        return 'custom_pages_template_richtext_content';
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
        $clone = new RichtextContent();
        $clone->content = $this->content;
        return $clone;
    }

    public function render($options = [])
    {   
        if($this->isEditMode($options)) {
            return $this->wrap('div', $this->purify($this->content), $options);
        } 
        
        return $this->purify($this->content);
    }
    
    public function renderEmpty($options = [])
    {
        return $this->renderEmptyDiv(Yii::t('CustomPagesModule.models_RichtextContent', 'Empty Richtext'), $options);
    }

    public function renderForm($form)
    {
        return TemplateContentFormFields::widget([
            'type' => 'richtext',
            'form' => $form,
            'model' => $this
        ]);
    }

    /**
     * @param null $user
     * @return bool
     */
    public function canEdit($user = null): bool
    {
        return PagePermission::canEdit();
    }

}
