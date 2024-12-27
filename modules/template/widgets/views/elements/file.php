<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\modules\custom_pages\modules\template\elements\FileElement;
use humhub\modules\custom_pages\modules\template\widgets\DeleteContentButton;
use humhub\modules\file\widgets\FilePreview;
use humhub\modules\file\widgets\UploadButton;
use humhub\modules\file\widgets\UploadProgress;
use humhub\modules\ui\form\widgets\ActiveForm;

/* @var $model FileElement */
/* @var $form ActiveForm */

$id = 'fileElement-' . $model->id;
?>

<?= $form->field($model, 'file_guid')->hiddenInput(['class' => 'file-guid'])->label(false) ?>

<div id="<?= $id ?>" class="file-upload-container clearfix">
    <div class="row">
        <div class="col-md-4 uploadContainer">
            <?= UploadButton::widget([
                'cssButtonClass' => 'btn-primary',
                'model' => $model,
                'single' => true,
                'label' => true,
                'attribute' => 'file_guid',
                'dropZone' => '#' . $id,
                'tooltip' => false,
                'preview' => '#' . $id . '-preview',
                'progress' => '#' . $id . '-progress',
            ]) ?>
            <?= DeleteContentButton::widget([
                'model' => $model,
                'previewId' => $id . '-preview',
            ]) ?>
        </div>
        <div class="col-md-8 previewContainer">
            <?= FilePreview::widget([
                'id' => $id . '-preview',
                'popoverPosition' => 'top',
                'items' => [$model->getFile()],
            ]) ?>
            <?= UploadProgress::widget(['id' => $id . '-progress']) ?>
        </div>
    </div>
</div>
