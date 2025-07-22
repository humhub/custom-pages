<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\libs\Html;
use humhub\modules\custom_pages\models\CustomPage;
use humhub\modules\ui\form\widgets\ActiveForm;
use humhub\widgets\ModalButton;
use humhub\widgets\ModalDialog;

/* @var $page CustomPage */
?>
<?php ModalDialog::begin([
    'header' => Yii::t('CustomPagesModule.view', '<strong>Copy</strong> Custom Page'),
    'closable' => true,
    'showClose' => true,
]) ?>
<?php $form = ActiveForm::begin() ?>
<div class="modal-body">
    <?php if ($page->isAllowedField('title')) : ?>
        <?= $form->field($page, 'title') ?>
    <?php endif; ?>

    <?php if (!$page->isSnippet()) : ?>
        <div class="alert alert-info infoAdminOnly"<?= !$page->isVisibility($page::VISIBILITY_ADMIN) ? ' style="display:none"' : '' ?>>
            <?= Yii::t('CustomPagesModule.view', '<strong>Info: </strong> Pages marked as "Admin Only" are not shown in the stream!'); ?>
        </div>
    <?php endif; ?>

    <?= $form->field($page, 'visibility')->radioList($page->getVisibilitySelection()) ?>
    <?= $form->field($page, 'target')->dropDownList($page->getAvailableTargetOptions()) ?>
</div>
<div class="modal-footer">
    <?= ModalButton::cancel() ?>
    <?= ModalButton::submitModal(null, Yii::t('CustomPagesModule.view', 'Copy')) ?>
</div>
<script <?= Html::nonce() ?>>
    $('input[type="radio"][name="CustomPage[visibility]"]').click(function () {
        $('.infoAdminOnly').toggle($(this).val() == <?= CustomPage::VISIBILITY_ADMIN ?>);
    });
</script>
<?php ActiveForm::end() ?>
<?php ModalDialog::end() ?>
