<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\modules\custom_pages\modules\template\elements\FileDownloadElement;
use humhub\modules\custom_pages\modules\template\widgets\CollapsableFormGroup;
use humhub\modules\file\widgets\FilePreview;
use humhub\modules\file\widgets\UploadButton;
use humhub\modules\file\widgets\UploadProgress;
use humhub\modules\ui\form\widgets\ActiveForm;

/* @var $model FileDownloadElement */
/* @var $form ActiveForm */

$id = 'fileDownloadElement-' . $model->id;
?>
<?= $form->field($model, 'title') ?>
<?= $form->field($model, 'showFileinfo')->checkbox() ?>
<?= $form->field($model, 'showIcon')->checkbox() ?>

<div id="<?= $id ?>" class="file-upload-container clearfix">
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
        'buttonOptions' => ['style' => 'float:left;'],
    ]) ?>

    <?= FilePreview::widget([
        'id' => $id . '-preview',
        'popoverPosition' => 'top',
        'items' => [$model->getFile()],
        'options' => ['style' => 'display:block;margin-left:150px']]) ?>
    <?= UploadProgress::widget([
        'id' => $id . '-progress',
        'options' => ['style' => 'display:block;margin-left:150px;width:500px'],
    ]) ?>
</div>

<?php CollapsableFormGroup::begin([
    'defaultState' => false,
    'label' => Yii::t('CustomPagesModule.base', 'Advanced'),
]) ?>
    <?= $form->field($model, 'style') ?>
    <?= $form->field($model, 'cssClass') ?>
    <?= $form->field($model, 'file_guid')->hiddenInput(['class' => 'file-guid'])->label(false) ?>
<?php CollapsableFormGroup::end() ?>
