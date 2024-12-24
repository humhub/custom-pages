<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\modules\custom_pages\modules\template\models\forms\ImportForm;
use humhub\modules\ui\form\widgets\ActiveForm;
use humhub\modules\ui\view\components\View;
use humhub\widgets\Button;
use humhub\widgets\ModalButton;
use humhub\widgets\ModalDialog;

/* @var $this View */
/* @var $model ImportForm */
?>

<?php ModalDialog::begin([
    'header' => Yii::t('CustomPagesModule.template', '<strong>Import</strong> {type}', ['type' => $model->type]),
]) ?>

<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

<div class="modal-body">
    <div class="alert alert-warning">
        <?= Yii::t('CustomPagesModule.template', 'Please note a template source with the same name will be replaced with data from your import file!') ?>
    </div>

    <?= $form->field($model, 'type')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'file')->fileInput() ?>
</div>

<div class="modal-footer">
    <?= ModalButton::cancel() ?>
    <?= Button::primary(Yii::t('CustomPagesModule.template', 'Import'))->submit() ?>
</div>

<?php ActiveForm::end() ?>

<?php ModalDialog::end() ?>