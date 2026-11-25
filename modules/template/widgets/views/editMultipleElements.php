<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\helpers\Html;
use humhub\modules\custom_pages\modules\template\elements\ContainerElement;
use humhub\modules\custom_pages\modules\template\models\forms\EditMultipleElementsForm;
use humhub\modules\custom_pages\modules\template\widgets\TemplateContentFormFields;
use humhub\widgets\bootstrap\Badge;
use humhub\widgets\modal\Modal;
use humhub\widgets\modal\ModalButton;

/* @var $model EditMultipleElementsForm */
/* @var $title string */
/* @var $backUrl string|null */

$footer = $backUrl ? ModalButton::light(Yii::t('CustomPagesModule.base', 'Back'))->load($backUrl) : '';
$footer .= $model->hasEditableFields()
    ? ModalButton::cancel() .
    ModalButton::save()
        ->submit()
        ->action($model->elementFilter !== null ? 'editInlineElementSubmit' : 'editMultipleElementsSubmit', null, '#templatePageRoot')
    : ModalButton::cancel($backUrl ? null : Yii::t('CustomPagesModule.base', 'Back'));
?>
<?php $form = Modal::beginFormDialog([
    'title' => $title,
    'size' => empty($model->contentMap) ? Modal::SIZE_DEFAULT : Modal::SIZE_LARGE,
    'footer' => $footer,
    'form' => ['enableClientValidation' => false],
]) ?>
<?= Html::hiddenInput('editMultipleElements', true); ?>

<div class="template-edit-multiple">
    <?php $counter = 0 ?>
    <?php foreach ($model->contentMap as $contentItem) : ?>

        <?php $isContainer = $contentItem->content instanceof ContainerElement; ?>
        <?php $isEmpty = $isContainer ? !$contentItem->content->hasItems() : $contentItem->content->isNewRecord; ?>

        <div class="card content-card">
            <div class="template-edit-multiple-tab card-header" tabindex="0">
                <div class="card-title">
                    <strong>
                        <?= Html::encode($model->getElement($contentItem->elementContent->element->name)->getTitle()) ?>&nbsp;
                        <i class="switchIcon ti ti-caret-down-filled" aria-hidden="true"></i>
                    </strong>
                </div>
                <div class="card-header-actions">
                    <?php if ($isContainer && $contentItem->content->definition?->allow_multiple): ?>
                        <?= Badge::success(Yii::t('CustomPagesModule.view', 'Multiple')) ?>
                    <?php endif; ?>
                    <?php if ($isEmpty): ?>
                        <?= Badge::warning(Yii::t('CustomPagesModule.view', 'Empty')) ?>
                    <?php endif; ?>
                    <?= Badge::success($contentItem->elementContent->label) ?>
                </div>
            </div>
            <div class="card-body<?= $counter != 0 ? ' d-none' : '' ?>" data-element-index="<?= $counter ?>">
                <?= TemplateContentFormFields::widget(['form' => $form, 'model' => $contentItem->content]) ?>
            </div>
            <div class="card-footer">&nbsp;</div>
        </div>
        <?php $counter++ ?>
    <?php endforeach; ?>

    <?php if (empty($model->contentMap)) : ?>
        <div class="text-center">
            <?= Yii::t('CustomPagesModule.view', 'This section has no editable elements.') ?>
        </div>
    <?php endif; ?>
</div>

<?php Modal::endFormDialog() ?>

<?= Html::script(' $(\'.template-edit-multiple-tab:first\').focus();')?>
