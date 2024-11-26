<?php

/**
 * Created by PhpStorm.
 * User: kingb
 * Date: 13.02.2019
 * Time: 13:29
 */

namespace humhub\modules\custom_pages\types;

use humhub\modules\custom_pages\models\CustomPage;
use Yii;
use yii\widgets\ActiveForm;

class IframeType extends ContentType
{
    public const ID = 3;

    /**
     * @inheritdoc
     */
    protected bool $hasContent = false;

    public function getLabel(): string
    {
        return Yii::t('CustomPagesModule.base', 'Iframe');
    }

    public function getDescription(): string
    {
        return Yii::t('CustomPagesModule.base', 'Will embed the result of a given url as an iframe element.');
    }

    public function render(CustomPage $content, $options = []): string
    {
        // TODO: Implement render() method.
        return '';
    }

    public function getViewName(): string
    {
        return 'iframe';
    }

    public function renderFormField(ActiveForm $form, CustomPage $page): string
    {
        $targetUrlField = $form->field($page, 'page_content')
            ->label($page->getAttributeLabel('targetUrl'));

        if (Yii::$app->user->isAdmin()) {
            $formField = $targetUrlField
                ->hint(Yii::t('CustomPagesModule.view', 'e.g. http://www.example.de'));
            $formField .= $form->field($page, 'iframe_attrs')
                ->hint(Yii::t('CustomPagesModule.view', 'e.g. allowfullscreen allow="camera; microphone"'));
        } else {
            $formField = $targetUrlField->textInput(['disabled' => 'disabled'])
                ->hint(Yii::t('CustomPagesModule.view', 'You need to be a system administrator to edit this URL'));
        }

        return $formField;
    }
}
