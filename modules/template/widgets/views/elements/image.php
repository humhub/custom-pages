<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\modules\custom_pages\modules\template\elements\ImageElement;
use humhub\modules\custom_pages\modules\template\widgets\CollapsableFormGroup;
use humhub\modules\custom_pages\modules\template\widgets\DeleteContentButton;
use humhub\modules\file\widgets\FilePreview;
use humhub\modules\file\widgets\UploadButton;
use humhub\modules\file\widgets\UploadProgress;
use humhub\modules\ui\form\widgets\ActiveForm;

/* @var ImageElement $model*/
/* @var ActiveForm $form */
/* @var bool $isAdminEdit */

$disableDefinition = !$isAdminEdit;

$id = 'imageElement-' . $model->id;
?>

<?= $form->field($model, 'file_guid')->hiddenInput(['class' => 'file-guid'])->label(false) ?>

<div id="<?= $id ?>">
    <div class="row">
        <div class="col-md-4 uploadContainer">
            <?= UploadButton::widget([
                'cssButtonClass' => 'btn-primary',
                'model' => $model,
                'single' => true,
                'label' => true,
                'attribute' => 'file_guid',
                'dropZone' => '#' . $id,
                'tooltip' => Yii::t('CustomPagesModule.base', 'Upload image'),
                'preview' => '#' . $id . '-preview',
                'progress' => '#' . $id . '-progress',
            ]) ?>
            <?= DeleteContentButton::widget([
                'model' => $model,
                'previewId' => $id . '-preview',
            ]) ?>
        </div>
        <?= UploadProgress::widget(['id' => $id . '-progress', 'options' => ['style' => 'width:500px']]) ?>
        <?= FilePreview::widget([
            'id' => $id . '-preview',
            'items' => [$model->getFile()],
            'jsWidget' => 'custom_pages.template.ImagePreview',
            'options' => ['class' => 'col-md-8 previewContainer'],
        ]) ?>
    </div>

    <br>

    <?php CollapsableFormGroup::begin(['defaultState' => false]) ?>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model->definition, 'height')->textInput(['disabled' => $disableDefinition]) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model->definition, 'width')->textInput(['disabled' => $disableDefinition]) ?>
            </div>
        </div>

        <?= $form->field($model->definition, 'style')->textInput(['disabled' => $disableDefinition]) ?>
        <?= $form->field($model, 'alt') ?>

    <?php CollapsableFormGroup::end() ?>
</div>
