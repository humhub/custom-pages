<?php

namespace humhub\modules\custom_pages\modules\template\models;

use yii\base\Model;

class ContainerItemVariable extends Model
{
    /**
     * @var ContainerContentItem 
     */
    public $item;
    
    public function getTitle()
    {
        return $this->item->title;
    }
}
