<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\modules\custom_pages\modules\template\models\forms\TemplateElementForm;
use humhub\modules\custom_pages\modules\template\widgets\EditContentSeperator;
use humhub\modules\custom_pages\modules\template\widgets\TemplateContentFormFields;
use humhub\widgets\bootstrap\Badge;
use humhub\widgets\modal\Modal;
use humhub\widgets\modal\ModalButton;
use yii\helpers\Html;

/* @var TemplateElementForm $model */
/* @var string $title */
/* @var bool $isAdminEdit */
/* @var string $resetUrl */

$canEdit = $model->element->template->canEdit();

$buttons = ModalButton::cancel();

if ($canEdit) {
    $buttons .= ModalButton::save()
        ->submit()
        ->action('editElementSubmit', null, '#templatePageRoot');
}

if ($canEdit && !$model->content->isNewRecord && $resetUrl != null) {
    $buttons .= ModalButton::danger(Yii::t('CustomPagesModule.base', 'Reset'))
        ->action('reset', $resetUrl, '#templatePageRoot');
}
?>
<?php $form = Modal::beginFormDialog([
    'title' => $title,
    'size' => Modal::SIZE_LARGE,
    'footer' => $buttons,
]) ?>
    <?= Badge::success($model->label)->cssClass('float-end') ?>
    <?php if (!$model->element->isNewRecord) : ?>
        #<strong><?= Html::encode($model->element->name) ?></strong>
     <?php endif; ?>
    <div class="clearfix" style="margin-bottom:10px"></div>

    <?php if ($model->element->isNewRecord) : ?>
        <?= $form->field($model->element, 'name')->textInput(['autofocus' => '']); ?>
    <?php else: ?>
        <div class="d-none">
            <?= $form->field($model->element, 'name')->hiddenInput()->label(false); ?>
        </div>
    <?php endif; ?>

    <?php if ($isAdminEdit) : ?>
        <?= $form->field($model->element, 'title')->textInput() ?>
    <?php endif; ?>

    <?= EditContentSeperator::widget(['isAdminEdit' => $isAdminEdit]) ?>

    <?= TemplateContentFormFields::widget(['form' => $form, 'model' => $model->content]) ?>
<?php Modal::endFormDialog() ?>
