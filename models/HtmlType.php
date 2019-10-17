<?php
/**
 * Created by PhpStorm.
 * User: kingb
 * Date: 13.02.2019
 * Time: 13:29
 */

namespace humhub\modules\custom_pages\models;


use Yii;
use yii\widgets\ActiveForm;

class HtmlType extends ContentType
{

    const ID = 2;

    function getId()
    {
        return static::ID;
    }

    function getLabel()
    {
        return Yii::t('CustomPagesModule.base', 'Html');
    }

    function getDescription()
    {
        return Yii::t('CustomPagesModule.base', 'Adds plain HTML content to your site.');
    }

    public function render(CustomContentContainer $content, $options = [])
    {
        // TODO: Implement getRender() method.
    }

    public function getViewName()
    {
        return 'html';
    }

    public function renderFormField(ActiveForm $form, CustomContentContainer $page)
    {
        $field = $form->field($page, $page->getPageContentProperty())->textarea(['id' => 'html_content', 'class' => 'form-control', 'rows' => '15']);
        return $field;
    }
}