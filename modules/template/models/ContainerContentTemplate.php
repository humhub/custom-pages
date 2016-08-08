<?php

namespace humhub\modules\custom_pages\modules\template\models;

/**
 * This is the model class for table "custom_pages_page".
 *
 * The followings are the available columns in table 'custom_pages_page':
 */
 class ContainerContentTemplate extends \humhub\components\ActiveRecord
{    
    /**
     * @return string the associated database table name
     */
    public static function tableName()
    {
        return 'custom_pages_template_container_content_template';
    }
    
    public function rules()
    {
        return [
            [['template_id', 'definition_id'], 'required'],
            [['template_id', 'definition_id'], 'integer']
        ];
    }
    
    public function getTemplate()
    {
        return $this->hasOne(Template::className(), ['id' => 'template_id']);
    }
    
    public function getContent()
    {
        return $this->hasOne(ContainerContent::className(), ['id' => 'definition_id']);
    }
}
