<?php

namespace humhub\modules\custom_pages\models;

use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\space\models\Space;
use Yii;

abstract class PageType
{
    public const Page = 'page';
    public const Snippet = 'snippet';

    public static function getContentName($type, ?string $containerClass = null)
    {
        switch ($type) {
            case static::Page:
                return $containerClass === Space::class
                    ? Yii::t('CustomPagesModule.base', 'Space Page')
                    : Yii::t('CustomPagesModule.base', 'Global Page');
            case static::Snippet:
                return $containerClass === Space::class
                    ? Yii::t('CustomPagesModule.base', 'Space Widget')
                    : Yii::t('CustomPagesModule.base', 'Global Widget');
        }
    }
}
