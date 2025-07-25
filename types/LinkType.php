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

class LinkType extends ContentType
{
    public const ID = 1;

    /**
     * @inheritdoc
     */
    protected bool $hasContent = false;

    public function getLabel(): string
    {
        return Yii::t('CustomPagesModule.base', 'Link');
    }

    public function getDescription(): string
    {
        return  Yii::t('CustomPagesModule.base', 'Will redirect requests to a given (relative or absolute) url.');
    }

    public function render(CustomPage $content, $options = []): string
    {
        // TODO: Implement render() method.
        return '';
    }

    public function getViewName(): string
    {
        return '';
    }

    public function renderFormField(ActiveForm $form, CustomPage $page): string
    {
        return $form->field($page, 'page_content')
            ->textInput(['class' => 'form-control'])
            ->label($page->getAttributeLabel('targetUrl'))
            ->hint(Yii::t('CustomPagesModule.view', 'e.g. http://www.example.de'));
    }
}
