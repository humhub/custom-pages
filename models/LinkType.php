<?php
/**
 * Created by PhpStorm.
 * User: kingb
 * Date: 13.02.2019
 * Time: 13:29
 */

namespace humhub\modules\custom_pages\models;


use Yii;

class LinkType extends ContentType
{

    const ID = 1;

    protected $hasContent = false;

    protected $isUrlContent = true;

    function getId()
    {
        return static::ID;
    }

    function getLabel()
    {
        return Yii::t('CustomPagesModule.base', 'Link');
    }

    function getDescription()
    {
       return  Yii::t('CustomPagesModule.base', 'Will redirect requests to a given (relative or absolute) url.');
    }
}