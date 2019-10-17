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

class PhpType extends ContentType
{

    const ID = 6;

    function getId()
    {
        return static::ID;
    }

    function getLabel()
    {
        return Yii::t('CustomPagesModule.base', 'PHP');
    }

    public function getContentLabel() {
        return Yii::t('CustomPagesModule.form_labels', 'View');
    }

    function getDescription()
    {
        return Yii::t('CustomPagesModule.base', 'With PHP based pages you can create custom pages by means of view files in your file system. Please check the module configuration for more Information.');
    }

    public function render(CustomContentContainer $content, $options = [])
    {
        // TODO: Implement render() method.
    }

    public function getViewName()
    {
        return 'php';
    }

    public function renderFormField(ActiveForm $form, CustomContentContainer $page)
    {
        return  $form->field($page, $page->getPageContentProperty())->dropDownList($page->getAllowedPhpViewFileSelection());
    }
}