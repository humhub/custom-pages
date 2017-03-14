<?php
/* @var $model humhub\modules\custom_pages\modules\template\models\template\FileContent */
/* @var $form humhub\compat\CActiveForm */

use yii\helpers\Url;

$uploadUrl = Url::to(['/file/file/upload']);

$id = 'fileContent-' . $model->id;
?>

<?= $form->field($model, 'file_guid')->hiddenInput(['class' => 'file-guid'])->label(false); ?>

<div id="<?= $id ?>">
    <div class="row">
        <div class="col-md-4 uploadContainer">
            <?=
            \humhub\modules\file\widgets\UploadButton::widget([
                'cssButtonClass' => 'btn-primary',
                'model' => $model,
                'single' => true,
                'attribute' => 'file_guid',
                'dropZone' => '#' . $id,
                'tooltip' => Yii::t('CustomPagesModule.base', 'Upload file'),
                'preview' => '#' . $id . '-preview',
                'progress' => '#' . $id . '-progress'
            ])?>

            <br />
            <br />
        </div>
        <?= humhub\modules\file\widgets\UploadProgress::widget(['id' => $id . '-progress', 'options' => ['style' => 'width:500px']]) ?>
        <?= humhub\modules\file\widgets\FilePreview::widget([
            'id' => $id . '-preview',
            'popoverPosition' => 'top',
            'items' => [$model->getFile()],
            'options' => ['class' => 'col-md-8 previewContainer']]) ?>
    </div>
</div>