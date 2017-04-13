<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;

/* @var $model humhub\modules\custom_pages\modules\template\models\forms\EditMultipleElementsForm */
?>
<?php \humhub\widgets\ModalDialog::begin(['size' => (empty($model->contentMap)) ? 'normal' : 'large', 'header' => $title])?>
    <?php $form = ActiveForm::begin(['enableClientValidation' => false]); ?>
        <?= Html::hiddenInput('editMultipleElements', true); ?>

        <div class="modal-body media-body template-edit-multiple"> 
            <?php $counter = 0 ?>
            <?php foreach ($model->contentMap as $key => $contentItem) : ?>

                <?php $isContainer = $contentItem->content instanceof humhub\modules\custom_pages\modules\template\models\ContainerContent; ?>

                <div class="panel panel-default">
                    <div class="template-edit-multiple-tab panel-heading" tabindex="0">
                        <strong>
                            <?= Html::encode($model->getElement($contentItem->ownerContent->element_name)->getTitle()) ?>&nbsp;
                            <i class="switchIcon fa fa-caret-down" aria-hidden="true"></i>
                        </strong>
                        <small class="pull-right">
                            <span class="label label-success"><?= $contentItem->ownerContent->label ?></span>
                        </small>
                        <?php if ($contentItem->content->isNewRecord): ?>
                            <small class="pull-right" style="margin-right: 2px">
                                <span class="label label-warning"><?= Yii::t('CustomPagesModule.widgets_views_editMultipleElements', 'Empty') ?></span>
                            </small>
                        <?php endif; ?>
                        <?php if ($isContainer && $contentItem->content->definition->allow_multiple): ?>
                            <small class="pull-right" style="margin-right: 2px">
                                <span class="label label-success"><?= Yii::t('CustomPagesModule.widgets_views_editMultipleElements', 'Multiple') ?></span>
                            </small>
                        <?php endif; ?>
                        <?php if ($isContainer && $contentItem->content->definition->is_inline): ?>
                            <small class="pull-right" style="margin-right: 2px">
                                <span class="label label-success"><?= Yii::t('CustomPagesModule.widgets_views_editMultipleElements', 'Inline') ?></span>
                            </small>
                        <?php endif; ?>
                    </div>
                    <?php // This was only set for container elements before. ?>
                    <div class="panel-body" data-element-index="<?= $counter ?>" style="<?= ($counter != 0) ? 'display:none' : '' ?>">
                        <?= $contentItem->content->renderForm($form); ?>
                    </div>
                    <div class="panel-footer">&nbsp;</div>
                </div>
                <?php $counter++ ?>
            <?php endforeach; ?>

            <?php if (empty($model->contentMap)) : ?>
                <div class="text-center">
                    <?= Yii::t('CustomPagesModule.widgets_views_editMultipleElements', 'This template does not contain any elements yet.') ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="modal-footer">
            <?php if (!empty($model->contentMap)) : ?>
                <button data-action-click="editMultipleElementsSubmit" data-action-target="#templatePageRoot" type="submit"  class="btn btn-primary" data-ui-loader>
                    <?= Yii::t('CustomPagesModule.base', 'Save'); ?>
                </button>
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= Yii::t('CustomPagesModule.base', 'Cancel'); ?></button>
            <?php else: ?>
                <button type="button" class="btn btn-primary" data-dismiss="modal"><?= Yii::t('CustomPagesModule.base', 'Back'); ?></button>
            <?php endif; ?>
        </div>
    <?php ActiveForm::end(); ?>
<?php \humhub\widgets\ModalDialog::end(); ?>

<script type="text/javascript">
    $('.template-edit-multiple-tab:first').focus();
</script>

<style>
    /**
     * This prevents the select2 template selection to be shrunk if rendered within a hidden panel.
     */
    .field-templateelement-name .select2-container--humhub {
        width:100% !important;
    }
</style>