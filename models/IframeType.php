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
        return
            $form->field($page, $page->getPageContentProperty())->textInput(['class' => 'form-control'])->label($page->getAttributeLabel('targetUrl'))
            . '<div class="help-block">' . Yii::t('CustomPagesModule.views_common_edit', 'e.g. http://www.example.de') . '</div>'
            . $form->field($page, 'iframeAttr')->textInput(['class' => 'form-control'])->label($page->getAttributeLabel('iframeAttr'))
            . '<div class="help-block">' . Yii::t('CustomPagesModule.views_common_edit', 'e.g. allowfullscreen allow="camera; microphone"') . '</div>';
    }

    /**
     * @ineritdoc
     */
    public function afterSave($page, $insert, $changedAttributes)
    {
        $conditions = ['object_model' => get_class($page), 'object_id' => $page->id];
        $iframeAttr = IframeAttr::findOne($conditions) ?? new IframeAttr($conditions);
        $iframeAttr->attr = $page->iframeAttr;
        $iframeAttr->save();

        return parent::afterSave($page, $insert, $changedAttributes);
    }
}