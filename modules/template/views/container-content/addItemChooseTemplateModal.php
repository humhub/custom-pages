<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\widgets\modal\Modal;
use humhub\widgets\modal\ModalButton;
use yii\helpers\Html;

/* @var $allowedTemplateSelection array */
/* @var $action string */
?>
<?php Modal::beginFormDialog([
    'title' => Yii::t('CustomPagesModule.template', 'Choose a template'),
    'footer' => ModalButton::cancel() .
        ModalButton::primary(Yii::t('CustomPagesModule.base', 'Submit'))->submit(),
    'form' => [
        'action' => $action,
        'enableClientValidation' => false,
    ],
]) ?>
    <div class="mb-3 field-templateelement-name required">
        <label class="control-label" for="templateSelection"><?= Yii::t('CustomPagesModule.template', 'Template') ?></label>
        <?= Html::dropDownList('templateId', null, $allowedTemplateSelection, ['id' => 'templateSelection', 'class' => 'form-control', 'data-ui-select2' => '']) ?>
    </div>
<?php Modal::endFormDialog() ?>
