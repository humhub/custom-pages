<?php

/**
 * Created by PhpStorm.
 * User: kingb
 * Date: 13.02.2019
 * Time: 13:29
 */

namespace humhub\modules\custom_pages\types;

use humhub\modules\custom_pages\models\CustomPage;
use humhub\widgets\form\ActiveForm;
use Yii;

class PhpType extends ContentType
{
    public const ID = 6;

    public function getLabel(): string
    {
        return Yii::t('CustomPagesModule.base', 'PHP');
    }

    public function getDescription(): string
    {
        return Yii::t('CustomPagesModule.base', 'With PHP based pages you can create custom pages by means of view files in your file system. Please check the module configuration for more Information.');
    }

    public function render(CustomPage $content, $options = []): string
    {
        // TODO: Implement render() method.
        return '';
    }

    public function getViewName(): string
    {
        return 'php';
    }

    public function renderFormField(ActiveForm $form, CustomPage $page): string
    {
        return  $form->field($page, 'page_content')->dropDownList($page->getAllowedPhpViewFileSelection());
    }
}
