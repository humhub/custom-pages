<?php
/**
 * Created by PhpStorm.
 * User: kingb
 * Date: 13.02.2019
 * Time: 13:29
 */

namespace humhub\modules\custom_pages\models;


use humhub\modules\custom_pages\assets\HtmlAssets;
use humhub\modules\file\widgets\FilePreview;
use humhub\modules\file\widgets\UploadButton;
use humhub\modules\file\widgets\UploadProgress;
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
        HtmlAssets::register(Yii::$app->getView());

        $field = $form->field($page, $page->getPageContentProperty())->textarea(['id' => 'html_content', 'class' => 'form-control', 'rows' => '15']);

        $field .= '<div class="form-group">'
            . UploadButton::widget([
                'id' => 'custom-page-html-file-upload',
                'label' => Yii::t('CustomPagesModule.models_HtmlType', 'Attach Files'),
                'tooltip' => false,
                'progress' => '#custom-page-html-upload-progress',
                'preview' => '#custom-page-html-upload-preview',
                'cssButtonClass' => 'btn-default btn-sm',
                'model' => $page,
            ])
            . FilePreview::widget([
                'id' => 'custom-page-html-upload-preview',
                'options' => ['style' => 'margin-top:10px'],
                'model' => $page,
                'edit' => true
            ])
            . UploadProgress::widget(['id' => 'custom-page-html-upload-progress'])
        . '</div>';

        return $field;
    }

    /**
     * @inheritdoc
     */
    public function afterSave($page, $insert, $changedAttributes)
    {
        if (!parent::afterSave($page, $insert, $changedAttributes)) {
            return false;
        }

        if ($insert) {
            $page->fileManager->attach(Yii::$app->request->post('fileList'));
        }

        return true;
    }
}