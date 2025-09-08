<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\components\View;
use humhub\modules\custom_pages\models\CustomPage;
use humhub\widgets\modal\Modal;
use humhub\widgets\modal\ModalButton;

/* @var $this View */
/* @var $page CustomPage */
?>
<?php $form = Modal::beginFormDialog([
    'title' => Yii::t('CustomPagesModule.view', '<strong>Copy</strong> Custom Page'),
    'footer' => ModalButton::cancel() .
        ModalButton::primary(Yii::t('CustomPagesModule.view', 'Copy'))->submit(),
]) ?>
    <?php if ($page->isAllowedField('title')) : ?>
        <?= $form->field($page, 'title') ?>
    <?php endif; ?>

    <?php if (!$page->isSnippet()) : ?>
        <div class="alert alert-info infoAdminOnly<?= $page->visibilityService->isAdmin() ? '' : ' d-none' ?>">
            <?= Yii::t('CustomPagesModule.view', '<strong>Info: </strong> Pages marked as "Admin Only" are not shown in the stream!'); ?>
        </div>
    <?php endif; ?>

    <?= $this->render('edit_visibility', ['page' => $page, 'form' => $form]) ?>
    <?= $form->field($page, 'target')->dropDownList($page->getAvailableTargetOptions()) ?>

<?php Modal::endFormDialog() ?>
