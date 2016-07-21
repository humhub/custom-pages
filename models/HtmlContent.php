<?php

namespace humhub\modules\custom_pages\models;

use Yii;
use humhub\components\ActiveRecord;

/**
 * This is the model class for table "custom_pages_page".
 *
 * The followings are the available columns in table 'custom_pages_page':
 */
 class HtmlContent extends TemplateContentActiveRecord
{
    public static $label = 'HTML';
     
    /**
     * @return string the associated database table name
     */
    public static function tableName()
    {
        return 'custom_pages_template_content_html';
    }
    
    public function rules()
    {
        return [
            ['content', 'safe']
        ];
    }
    
    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'content' => 'Content',
        ]);
    }
    
    public function getLabel()
    {
        return self::$label;
    }

    public function render($options = [])
    {
        $attributes = '';
        foreach($options as $key => $value) {
            $attributes .= ' '.$key.'="'.$value.'"';
        }
        if($this->isNewRecord || $this->content == null) {
            return '<div '.$attributes.' style="text-align: center; min-height: 200px; background-color: lightgray; border: 5px solid white;"><strong>EDIT HTML</strong></div>';
        } else {
            return '<div '.$attributes.'>'.$this->content.'</div>';
        }
    }

    public function renderEditForm($form)
    {
        return \humhub\modules\custom_pages\widgets\HtmlContentEditForm::widget([
            'form' => $form,
            'model' => $this
        ]);
    }

}
