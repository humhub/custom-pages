<?php

namespace humhub\modules\custom_pages\models;

use Yii;
use humhub\components\ActiveRecord;

/**
 * This is the model class for table "custom_pages_template".
 */
class TemplateBlock extends ActiveRecord
{
    
    /**
     * @return string the associated database table name
     */
    public static function tableName()
    {
        return 'custom_pages_template_block';
    }

    public function rules()
    {
        return [
            [['name', 'type'], 'required'],
            [['name', 'type'], 'string', 'length' => [2, 100]],
            [['name'], 'match', 'pattern' => '/^[a-zA-Z][a-zA-Z0-9_]*/i'],
            [['name'], 'oneOwnerValidator'],
            [['template_id', 'page_template_id', 'template_content_id'], 'integer']
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('CustomPagesModule.models_TemplateBlock', 'Name')
        ];
    }
    
    public function oneOwnerValidator($attribute, $params)
    {
        if($this->template_id != null && $this->page_template_id != null) {
            $this->addError($attribute, Yii::t('CustomPagesModule.models_TemplateBlock', 'Two block owner are not allowed.'));
        } else if($this->template_id == null && $this->page_template_id == null) {
            $this->addError($attribute, Yii::t('CustomPagesModule.models_TemplateBlock', 'No block owner given.'));
        }
    }
    
    public function render()
    {
        $options = ['data-template-block' => $this->name];
        if(!$this->hasContent()) {
            return Yii::createObject($this->type)->render($options); // Render default output
        } else {
            return $this->content->render($options);
        }
    }
    
    public function getContent()
    {
        return $this->hasOne(TemplateContent::className(), ['id' => 'template_id']);
    }
    
    public function hasContent()
    {
        return $this->template_content_id != null;
    }
    
    public function isTemplateDefault()
    {
        return $this->template_id != null;
    }
    
    public function getLabel()
    {
        return Yii::createObject($this->type)->getLabel();
    }
}
