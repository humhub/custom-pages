<?php

namespace humhub\modules\custom_pages\models;

use Yii;
use humhub\components\ActiveRecord;

/**
 * This is the model class for table "custom_pages_template".
 */
class TemplateContent extends ActiveRecord
{
    public function behaviors()
    {
        return [
            [
                'class' => \humhub\components\behaviors\PolymorphicRelation::className(),
                'mustBeInstanceOf' => [TemplateContentActiveRecord::className()],
            ]
        ];
    }
    
    /**
     * @return string the associated database table name
     */
    public static function tableName()
    {
        return 'custom_pages_template_content';
    }
    
    public function render()
    {
        print '<pre>';
        print_r($this);
        die();
        
        return $this->getPolymorphicRelation()->render();
    }

    public function rules()
    {
        return [
            [['object_model', 'object_id'], 'required'],
            [['name'], 'string', 'length' => [5, 255]]
        ];
    }
}
