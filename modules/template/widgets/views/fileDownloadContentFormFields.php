<?php
use humhub\modules\custom_pages\modules\template\widgets\CollapsableFormGroup;
/* @var $model humhub\modules\custom_pages\modules\template\models\template\FileContent */
/* @var $form humhub\compat\CActiveForm */

use yii\helpers\Url;

$uploadUrl = Url::to(['/file/file/upload']);

$id = 'fileContent-' . $model->id;
?>



<?= $form->field($model, 'title')->textInput(); ?>
<?= $form->field($model, 'showFileinfo')->checkbox(); ?>
<?= $form->field($model, 'showIcon')->checkbox(); ?>

<div id="<?= $id ?>" class="file-upload-container clearfix">

    <?=
    \humhub\modules\file\widgets\UploadButton::widget([
        'cssButtonClass' => 'btn-primary',
        'model' => $model,
        'single' => true,
        'label' => true,
        'attribute' => 'file_guid',
        'dropZone' => '#' . $id,
        'tooltip' => false,
        'preview' => '#' . $id . '-preview',
        'progress' => '#' . $id . '-progress',
        'buttonOptions' =>  ['style' => 'float:left;']
    ])?>


    <?= humhub\modules\file\widgets\FilePreview::widget([
        'id' => $id . '-preview',
        'popoverPosition' => 'top',
        'items' => [$model->getFile()],
        'options' => ['style' => 'display:block;margin-left:150px']]) ?>
    <?= humhub\modules\file\widgets\UploadProgress::widget(['id' => $id . '-progress', 'options' => ['style' => 'display:block;margin-left:150px;width:500px']]) ?>

</div>

<?php CollapsableFormGroup::begin(['defaultState' => false, 'label' => Yii::t('CustomPagesModule.base', 'Advanced'),]) ?>
    <?= $form->field($model, 'style')->textInput(); ?>
    <?= $form->field($model, 'cssClass')->textInput(); ?>
    <?= $form->field($model, 'file_guid')->hiddenInput(['class' => 'file-guid'])->label(false); ?>
<?php CollapsableFormGroup::end() ?>

