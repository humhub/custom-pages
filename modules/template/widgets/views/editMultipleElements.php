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
?>
<?php $form = Modal::beginFormDialog([
    'title' => $title,
    'size' => empty($model->contentMap) ? Modal::SIZE_DEFAULT : Modal::SIZE_LARGE,
    'footer' => empty($model->contentMap)
        ? ModalButton::cancel(Yii::t('CustomPagesModule.base', 'Back'))
        : ModalButton::cancel() .
        ModalButton::save()
            ->submit()
            ->action('editMultipleElementsSubmit', null, '#templatePageRoot'),
    'form' => ['enableClientValidation' => false],
]) ?>
<?= Html::hiddenInput('editMultipleElements', true); ?>

<div class="template-edit-multiple">
    <?php $counter = 0 ?>
    <?php foreach ($model->contentMap as $key => $contentItem) : ?>

        <?php $isContainer = $contentItem->content instanceof ContainerElement; ?>

        <div class="panel panel-default">
            <div class="template-edit-multiple-tab panel-heading" tabindex="0">
                <strong>
                    <?= Html::encode($model->getElement($contentItem->elementContent->element->name)->getTitle()) ?>&nbsp;
                    <i class="switchIcon fa fa-caret-down" aria-hidden="true"></i>
                </strong>
                <?= Badge::success($contentItem->elementContent->label)->right() ?>
                <?php if ($contentItem->content->isNewRecord): ?>
                    <?= Badge::warning(Yii::t('CustomPagesModule.view', 'Empty'))->right() ?>
                <?php endif; ?>
                <?php if ($isContainer && $contentItem->content->definition->allow_multiple): ?>
                    <?= Badge::success(Yii::t('CustomPagesModule.view', 'Multiple'))->right() ?>
                <?php endif; ?>
            </div>
            <?php // This was only set for container elements before. ?>
            <div class="panel-body<?= $counter != 0 ? ' d-none' : '' ?>" data-element-index="<?= $counter ?>">
                <?= TemplateContentFormFields::widget(['form' => $form, 'model' => $contentItem->content]) ?>
            </div>
            <div class="panel-footer">&nbsp;</div>
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

<style>
    /**
     * This prevents the select2 template selection to be shrunk if rendered within a hidden panel.
     */
    .field-templateelement-name .select2-container--humhub {
        width:100% !important;
    }
</style>
