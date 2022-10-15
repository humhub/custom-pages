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

    const ID = 3;

    protected $hasContent = false;


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
        return Yii::t('CustomPagesModule.base', 'Will embed the the result of a given url as an iframe element.');
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
        $targetUrlField = $form->field($page, $page->getPageContentProperty())->label($page->getAttributeLabel('targetUrl'));
        if ($page->iframe_attrs && !Yii::$app->user->isAdmin()) {
            $formField = $targetUrlField->textInput(['class' => 'form-control', 'disabled' => 'disabled'])->hint(Yii::t('CustomPagesModule.views_common_edit', 'You need to be a system administrator to edit this URL'));
        } else {
            $formField = $targetUrlField->textInput(['class' => 'form-control'])->hint(Yii::t('CustomPagesModule.views_common_edit', 'e.g. http://www.example.de'));
        }

        if (Yii::$app->user->isAdmin()) {
            $formField .=
                $form->field($page, 'iframe_attrs')->textInput(['class' => 'form-control'])->hint(Yii::t('CustomPagesModule.views_common_edit', 'e.g. allowfullscreen allow="camera; microphone"'));
        }

        return $formField;
    }
}