<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\helpers\Html;
use humhub\modules\custom_pages\models\CustomPage;
use humhub\widgets\modal\Modal;
use humhub\widgets\modal\ModalButton;

/* @var $page CustomPage */
?>
<?php $form = Modal::beginFormDialog([
    'title' => Yii::t('CustomPagesModule.view', '<strong>Copy</strong> Custom Page'),
    'footer' => ModalButton::cancel() .
        ModalButton::save(Yii::t('CustomPagesModule.view', 'Copy'))->submit(),
]) ?>
    <?php if ($page->isAllowedField('title')) : ?>
        <?= $form->field($page, 'title') ?>
    <?php endif; ?>

    <?php if (!$page->isSnippet()) : ?>
        <div class="alert alert-info infoAdminOnly<?= $page->visibility != CustomPage::VISIBILITY_ADMIN_ONLY ? ' d-none' : '' ?>">
            <?= Yii::t('CustomPagesModule.view', '<strong>Info: </strong> Pages marked as "Admin Only" are not shown in the stream!'); ?>
        </div>
    <?php endif; ?>

    <?= $form->field($page, 'visibility')->radioList($page->getVisibilitySelection()) ?>
    <?= $form->field($page, 'target')->dropDownList($page->getAvailableTargetOptions()) ?>

<script <?= Html::nonce() ?>>
    $('input[type="radio"][name="CustomPage[visibility]"]').click(function () {
        $('.infoAdminOnly').toggle($(this).val() == <?= CustomPage::VISIBILITY_ADMIN_ONLY ?>);
    });
</script>
<?php Modal::endFormDialog() ?>
