<?php

namespace humhub\modules\custom_pages\models;

use Yii;
use humhub\components\ActiveRecord;

/**
 * This is the model class for table "custom_pages_template".
 */
class PageTemplate extends ActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public static function tableName()
    {
        return 'custom_pages_page_template';
    }

    public function rules()
    {
        return [
            [['page_id', 'template_id'], 'required'],
            [['page_id', 'template_id'], 'integer'],
        ];
    }
    
    public function getBlocks()
    {
        $blocks = TemplateBlock::find()
                ->joinWith('content')
                ->where(['page_template_id' => $this->id])->all();
        
        $blockNames = array_map(function($block) {
            return $block->name;
        }, $blocks);
        
        $defaultBlocks = TemplateBlock::find()
                ->joinWith('content')
                ->where(['template_id' => $this->template_id])
                ->andWhere(['not in', 'custom_pages_template_block.name', $blockNames])->all();
        
        return array_merge($blocks, $defaultBlocks);
    }
    
    public function getPage()
    {
        return $this->hasOne(Page::className(), ['id' => 'page_id']);
    }

    public function getTemplate()
    {
        return $this->hasOne(Template::className(), ['id' => 'template_id']);
    }
}
