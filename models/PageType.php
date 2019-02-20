<?php


namespace humhub\modules\custom_pages\models;


use Yii;

abstract class PageType
{
    const Page = 'page';
    const Snippet = 'snippet';

    public static function getLabel($type)
    {
        switch ($type) {
            case static::Page:
                return Yii::t('CustomPagesModule.models_Page', 'page');
            case static::Snippet:
                return Yii::t('CustomPagesModule.models_ContainerSnippet', 'snippet');
        }
    }
}