<?php

namespace humhub\modules\custom_pages\modules\template\models;

use humhub\modules\custom_pages\modules\template\elements\ContainerItem;
use yii\base\Model;

class ContainerItemVariable extends Model
{
    /**
     * @var ContainerItem
     */
    public $item;

    public function getTitle()
    {
        return $this->item->title;
    }
}
