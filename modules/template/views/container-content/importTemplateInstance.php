<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\modules\custom_pages\modules\template\models\forms\ImportInstanceForm;
use humhub\widgets\Button;
use humhub\widgets\ModalButton;
use humhub\widgets\ModalDialog;
use yii\widgets\ActiveForm;

/* @var ImportInstanceForm $model */
?>
<?php ModalDialog::begin(['header' => Yii::t('CustomPagesModule.template', 'Import a template instance')]) ?>
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>
        <div class="modal-body">
            <?= $form->field($model, 'file')->fileInput() ?>
        </div>
        <div class="modal-footer">
            <?= ModalButton::cancel() ?>
            <?= Button::primary(Yii::t('CustomPagesModule.template', 'Import'))
                ->action('runImportTemplateInstance', null, '.cp-structure') ?>
        </div>
    <?php ActiveForm::end() ?>
<?php ModalDialog::end() ?>
