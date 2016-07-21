<?php

namespace humhub\modules\custom_pages\models;

use Yii;
use humhub\components\ActiveRecord;

/**
 * This is the model class for table "custom_pages_template".
 */
class Template extends ActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public static function tableName()
    {
        return 'custom_pages_template';
    }

    public function rules()
    {
        return [
           [['name', 'description'], 'required', 'on' => ['edit']],
           [['allow_for_spaces'], 'integer'],
            [['name'], 'unique'],
           [['name'], 'string', 'max' => 100],
           [['source'], 'required', 'on' => ['source']],
        ];
    }
    
    public function getBlocks()
    {
        return $this->hasMany(TemplateBlock::className(), ['template_id' => 'id']);
    }
    
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['edit'] = ['name', 'description'];
        $scenarios['source'] = ['source'];
        return $scenarios;
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'name' => 'Name',
            'source' => 'Source',
            'allow_for_spaces' => 'Allow this template in spaces',
            'description' => 'Description',
        );
    }
    
    public static function getSelection()
    {
        return \yii\helpers\ArrayHelper::map(self::find()->all(), 'id', 'name');
    }
}
