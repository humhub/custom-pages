<?php

namespace humhub\modules\custom_pages\models;

use Yii;
use humhub\components\ActiveRecord;

/**
 * This is the model class for table "custom_pages_page".
 *
 * The followings are the available columns in table 'custom_pages_page':
 */
abstract class TemplateContentActiveRecord extends ActiveRecord
{
    public $name;
    
    public function attributeLabels()
    {
        return [
            'name' => 'Name'
        ];
    }
    
    /**
     * Returns the rendered template block
     */
    abstract public function render($options = []);
    
    abstract public function getLabel();
    
    abstract public function renderEditForm($form);
}
