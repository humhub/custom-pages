<?php

namespace humhub\modules\custom_pages\models;

use humhub\modules\content\components\ContentContainerActiveRecord;
use Yii;

abstract class PageType
{
    public const Page = 'page';
    public const Snippet = 'snippet';

    public static function getContentName($type, ?ContentContainerActiveRecord $container = null)
    {
        switch ($type) {
            case static::Page:
                return $container === null
                    ? Yii::t('CustomPagesModule.models_Page', 'Global Page')
                    : Yii::t('CustomPagesModule.models_Page', 'Space Page');
            case static::Snippet:
                return $container === null
                    ? Yii::t('CustomPagesModule.models_ContainerSnippet', 'Global Widget')
                    : Yii::t('CustomPagesModule.models_ContainerSnippet', 'Space Widget');
        }
    }
}
