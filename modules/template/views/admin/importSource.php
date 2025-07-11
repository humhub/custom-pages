<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\modules\custom_pages\modules\template\models\forms\ImportForm;
use humhub\widgets\modal\Modal;
use humhub\widgets\modal\ModalButton;

/* @var $model ImportForm */
?>
<?php $form = Modal::beginFormDialog([
    'title' => Yii::t('CustomPagesModule.template', '<strong>Import</strong> Template'),
    'footer' => ModalButton::cancel() .
        ModalButton::save(Yii::t('CustomPagesModule.template', 'Import'))->submit(),
    'form' => ['options' => ['enctype' => 'multipart/form-data']],
]) ?>
    <div class="alert alert-warning">
        <?= Yii::t('CustomPagesModule.template', 'If a template with the same name already exists, it will be replaced with the data from your import file.') ?>
    </div>
    <?= $form->field($model, 'file')->fileInput() ?>
<?php Modal::endFormDialog() ?>
