<?php
/* @var $model humhub\modules\custom_pages\modules\template\models\template\ImageContent */
/* @var $form humhub\compat\CActiveForm */

use yii\helpers\Url;
use yii\helpers\Html;
use humhub\modules\custom_pages\modules\template\widgets\CollapsableFormGroup;

$uploadUrl = Url::to(['/file/file/upload']);

$disableDefinition = !$isAdminEdit && $model->definition->is_default;

$id = 'imageContent-' . $model->id;
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
                'tooltip' => Yii::t('CustomPagesModule.base', 'Upload image'),
                'preview' => '#' . $id . '-preview',
                'progress' => '#' . $id . '-progress'
            ])?>

            <br />
            <br />
        </div>
        <?= humhub\modules\file\widgets\UploadProgress::widget(['id' => $id . '-progress', 'options' => ['style' => 'width:500px']]) ?>
        <?= humhub\modules\file\widgets\FilePreview::widget([
            'id' => $id . '-preview',
            'items' => [$model->getFile()],
            'jsWidget' => 'custom_pages.template.ImagePreview',
            'options' => ['class' => 'col-md-8 previewContainer']]) ?>

    </div>
    
    <br />

    <?php CollapsableFormGroup::begin(['defaultState' => false]) ?>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model->definition, 'height')->textInput(['disabled' => $disableDefinition]); ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model->definition, 'width')->textInput(['disabled' => $disableDefinition]); ?>
            </div>
        </div>
    
        <?= $form->field($model->definition, 'style')->textInput(['disabled' => $disableDefinition]); ?>    
        <?= $form->field($model, 'alt')->textInput(); ?>
    
    <?php CollapsableFormGroup::end() ?>
</div>