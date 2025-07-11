<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\modules\custom_pages\modules\template\models\forms\ImportInstanceForm;
use humhub\widgets\modal\Modal;
use humhub\widgets\modal\ModalButton;

/* @var ImportInstanceForm $model */
?>
<?php $form = Modal::beginFormDialog([
    'title' => Yii::t('CustomPagesModule.template', 'Import a template instance'),
    'footer' => ModalButton::cancel() .
        ModalButton::save(Yii::t('CustomPagesModule.template', 'Import'))
            ->submit()
            ->action('runImportTemplateInstance', null, '.cp-structure'),
    'form' => [
        'enableClientValidation' => false,
        'options' => ['enctype' => 'multipart/form-data'],
    ],
]) ?>
    <?= $form->field($model, 'file')->fileInput() ?>
    <?= $form->field($model, 'replace')->checkbox(['disabled' => $model->getService()->isReplaced()]) ?>
<?php Modal::endFormDialog() ?>
