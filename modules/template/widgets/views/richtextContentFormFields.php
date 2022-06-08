<?php
use humhub\modules\custom_pages\modules\template\models\RichtextContent;
use humhub\modules\custom_pages\widgets\TinyMce;
use humhub\modules\file\widgets\FilePreview;
use humhub\modules\file\widgets\UploadButton;
use humhub\modules\file\widgets\UploadProgress;
use humhub\modules\ui\form\widgets\ActiveForm;

/* @var $model RichtextContent */
/* @var $form ActiveForm */

$id = $model->id ?? str_replace(['[', ']'], '', $model->formName());
?>
<?= $form->field($model, 'content')->widget(TinyMce::class, [
    'options' => [
        'id' => 'richtext_' . $id,
        'class' => 'tinymceInput',
        'rows' => 6
    ],
    'clientOptions' => [
        'humhubTrigger' => [
            'icon' => 'upload',
            'text' => Yii::t('CustomPagesModule.models_HtmlType', 'Attach Files'),
            'selector' => '#richtext-template-file-uploader-' . $id,
            'event' => 'click'
        ]
    ]])->label(false) ?>

<?= '<div class="form-group">' . UploadButton::widget([
        'id' => 'richtext-template-file-uploader-' . $id,
        'label' => Yii::t('CustomPagesModule.models_HtmlType', 'Attach Files'),
        'tooltip' => false,
        'progress' => '#richtext-template-file-uploader-progress-' . $id,
        'preview' => '#richtext-template-file-uploader-preview-' . $id,
        'cssButtonClass' => 'btn-default btn-sm',
        'model' => $model,
        'submitName' => $model->formName().'[fileList][]'
    ]) . FilePreview::widget([
        'id' => 'richtext-template-file-uploader-preview-' . $id,
        'options' => ['style' => 'margin-top:10px'],
        'model' => $model,
        'edit' => true
    ]) . UploadProgress::widget(['id' => 'richtext-template-file-uploader-progress-' . $id]) .
'</div>' ?>