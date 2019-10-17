<?php


namespace humhub\modules\custom_pages\interfaces;

use humhub\modules\content\components\ContentContainerActiveRecord;
use yii\base\Event;

class CustomPagesEvent extends Event
{
    /**
     * @var ContentContainerActiveRecord
     */
    public $container;
}