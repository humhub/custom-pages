<?php
/**
 * Created by PhpStorm.
 * User: kingb
 * Date: 13.02.2019
 * Time: 13:29
 */

namespace humhub\modules\custom_pages\models;


use Yii;

class TemplateType extends ContentType
{

    const ID = 5;

    function getId()
    {
        return static::ID;
    }

    function getLabel()
    {
        return Yii::t('CustomPagesModule.base', 'Template');
    }

    function getDescription()
    {
       return Yii::t('CustomPagesModule.base', 'Templates allow you to define combinable page fragments with inline edit functionality.');
    }
}