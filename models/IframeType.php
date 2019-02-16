<?php
/**
 * Created by PhpStorm.
 * User: kingb
 * Date: 13.02.2019
 * Time: 13:29
 */

namespace humhub\modules\custom_pages\models;


use Yii;

class IframeType extends ContentType
{

    const ID = 3;

    protected $hasContent = false;

    protected $isUrlContent = true;


    function getId()
    {
        return static::ID;
    }

    function getLabel()
    {
        return Yii::t('CustomPagesModule.base', 'Iframe');
    }

    function getDescription()
    {
        return  Yii::t('CustomPagesModule.base', 'Will embed the the result of a given url as an iframe element.');
    }
}