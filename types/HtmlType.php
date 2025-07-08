<?php

/**
 * Created by PhpStorm.
 * User: kingb
 * Date: 13.02.2019
 * Time: 13:29
 */

namespace humhub\modules\custom_pages\types;

use humhub\modules\custom_pages\models\CustomPage;
use humhub\modules\custom_pages\widgets\TinyMce;
use humhub\modules\file\widgets\FilePreview;
use humhub\modules\file\widgets\UploadButton;
use humhub\modules\file\widgets\UploadProgress;
use humhub\widgets\form\ActiveForm;
use Yii;

class HtmlType extends ContentType
{
    public const ID = 2;

    public function getLabel(): string
    {
        return Yii::t('CustomPagesModule.base', 'Html');
    }

    public function getDescription(): string
    {
        return Yii::t('CustomPagesModule.base', 'Adds plain HTML content to your site.');
    }

    public function render(CustomPage $content, $options = []): string
    {
        // TODO: Implement getRender() method.
        return '';
    }

    public function getViewName(): string
    {
        return 'html';
    }

    public function renderFormField(ActiveForm $form, CustomPage $page): string
    {
        $field = $form->field($page, 'page_content')->widget(TinyMce::class, [
            'options' => ['id' => 'html_content'],
            'clientOptions' => [
                'humhubTrigger' => [
                    'icon' => 'upload',
                    'text' => Yii::t('CustomPagesModule.model', 'Attach Files'),
                    'selector' => '#custom-page-html-file-upload',
                    'event' => 'click',
                ],
            ],
        ]);

        $field .= '<div class="form-group">'
            . UploadButton::widget([
                'id' => 'custom-page-html-file-upload',
                'label' => Yii::t('CustomPagesModule.model', 'Attach Files'),
                'tooltip' => false,
                'hideInStream' => true,
                'progress' => '#custom-page-html-upload-progress',
                'preview' => '#custom-page-html-upload-preview',
                'cssButtonClass' => 'btn-default btn-sm',
                'model' => $page,
            ])
            . FilePreview::widget([
                'id' => 'custom-page-html-upload-preview',
                'options' => ['style' => 'margin-top:10px'],
                'model' => $page,
                'edit' => true,
            ])
            . UploadProgress::widget(['id' => 'custom-page-html-upload-progress'])
        . '</div>';

        return $field;
    }

    /**
     * @inheritdoc
     */
    public function afterSave(CustomPage $page, bool $insert, array $changedAttributes): bool
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
