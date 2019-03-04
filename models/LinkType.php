<?php
/**
 * Created by PhpStorm.
 * User: kingb
 * Date: 13.02.2019
 * Time: 13:29
 */

namespace humhub\modules\custom_pages\models;


use yii\widgets\ActiveForm;
use Yii;

class LinkType extends ContentType
{

    const ID = 1;

    protected $hasContent = false;

    function getId()
    {
        return static::ID;
    }

    function getLabel()
    {
        return Yii::t('CustomPagesModule.base', 'Link');
    }

    public function getContentLabel() {
        return Yii::t('CustomPagesModule.form_labels', 'Url');
    }

    function getDescription()
    {
       return  Yii::t('CustomPagesModule.base', 'Will redirect requests to a given (relative or absolute) url.');
    }

    public function render(CustomContentContainer $content, $options = [])
    {
        // TODO: Implement render() method.
    }

    public function getViewName()
    {
        return null;
    }

    public function renderFormField(ActiveForm $form, CustomContentContainer $page)
    {
        return $form->field($page, $page->getPageContentProperty())->textInput(['class' => 'form-control'])->label($page->getAttributeLabel('targetUrl'))
            .'<div class="help-block">'.Yii::t('CustomPagesModule.views_common_edit', 'e.g. http://www.example.de').'</div>';
    }
}