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

class IframeType extends ContentType
{
    public const ID = 3;

    protected $hasContent = false;

    public function getId()
    {
        return static::ID;
    }

    public function getLabel()
    {
        return Yii::t('CustomPagesModule.base', 'Iframe');
    }

    public function getDescription()
    {
        return Yii::t('CustomPagesModule.base', 'Will embed the result of a given url as an iframe element.');
    }

    public function render(CustomContentContainer $content, $options = [])
    {
        // TODO: Implement render() method.
    }

    public function getViewName()
    {
        return 'iframe';
    }

    public function renderFormField(ActiveForm $form, CustomContentContainer $page)
    {
        $targetUrlField = $form->field($page, $page->getPageContentProperty())
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
