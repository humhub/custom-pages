<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\components\View;
use humhub\modules\custom_pages\modules\template\models\forms\ImportForm;
use humhub\widgets\bootstrap\Button;
use humhub\widgets\form\ActiveForm;
use humhub\widgets\modal\ModalButton;
use humhub\widgets\ModalDialog;

/* @var $this View */
/* @var $model ImportForm */
?>

<?php ModalDialog::begin([
    'header' => Yii::t('CustomPagesModule.template', '<strong>Import</strong> Template'),
]) ?>

<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

<div class="modal-body">
    <div class="alert alert-warning">
        <?= Yii::t('CustomPagesModule.template', 'If a template with the same name already exists, it will be replaced with the data from your import file.') ?>
    </div>

    <?= $form->field($model, 'file')->fileInput() ?>
</div>

<div class="modal-footer">
    <?= ModalButton::cancel() ?>
    <?= Button::primary(Yii::t('CustomPagesModule.template', 'Import'))->submit() ?>
</div>

<?php ActiveForm::end() ?>

<?php ModalDialog::end() ?>
