<?php

use humhub\modules\custom_pages\modules\template\elements\ContainerElement;
use humhub\modules\custom_pages\modules\template\models\forms\EditItemForm;
use humhub\modules\custom_pages\modules\template\widgets\TemplateContentFormFields;
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;

/* @var $model EditItemForm */
/* @var $action string */
/* @var $title string */
?>

<?php humhub\widgets\ModalDialog::begin(['header' => $title, 'size' => 'large']) ?>
    <?php $form = ActiveForm::begin(['action' => $action, 'enableClientValidation' => false]); ?>
        <?= $form->field($model, 'submitFlag')->hiddenInput()->label(false) ?>
        <div class="modal-body media-body template-edit-multiple">

            <?php $counter = 0 ?>
            <?php foreach ($model->contentMap as $key => $contentItem) : ?>

                <?php $isContainer = $contentItem->content instanceof ContainerElement; ?>

                <div class="panel panel-default">
                    <div class="template-edit-multiple-tab panel-heading" tabindex="0">
                        <strong>
                            <?= Html::encode($model->getElement($contentItem->elementContent->element->name)->getTitle()) ?>&nbsp;
                            <i class="switchIcon fa fa-caret-down" aria-hidden="true"></i>
                        </strong>
                        <small class="pull-right">
                            <span class="label label-success"><?= $contentItem->elementContent->getLabel() ?></span>
                        </small>
                        <?php if ($contentItem->content->isNewRecord): ?>
                            <small class="pull-right" style="margin-right: 2px">
                                <span class="label label-warning"><?= Yii::t('CustomPagesModule.view', 'Empty') ?></span>
                            </small>
                        <?php endif; ?>
                        <?php if ($isContainer && $contentItem->content->definition->allow_multiple): ?>
                            <small class="pull-right" style="margin-right: 2px">
                                <span class="label label-success"><?= Yii::t('CustomPagesModule.view', 'Multiple') ?></span>
                            </small>
                        <?php endif; ?>
                        <?php if ($isContainer && $contentItem->content->definition->is_inline): ?>
                            <small class="pull-right" style="margin-right: 2px">
                                <span class="label label-success"><?= Yii::t('CustomPagesModule.view', 'Inline') ?></span>
                            </small>
                        <?php endif; ?>
                    </div>
                    <?php // This was only set for container elements before.  ?>
                    <div class="panel-body" data-element-index="<?= $counter ?>" style="<?= ($counter != 0) ? 'display:none' : '' ?>">
                        <?= TemplateContentFormFields::widget(['form' => $form, 'model' => $contentItem->content]) ?>
                    </div>
                    <div class="panel-footer">&nbsp;</div>
                </div>
                <?php $counter++ ?>
            <?php endforeach; ?>

            <?php if (empty($model->contentMap)) : ?>
                <div class="text-center">
                    <?= Yii::t('CustomPagesModule.view', 'This template does not contain any elements yet.') ?>
                </div>
            <?php endif; ?>

        </div>
        <div class="modal-footer">
            <button data-action-click="editItemSubmit" data-action-target="#templatePageRoot" type="submit"  class="btn btn-primary" data-ui-loader>
                <?= Yii::t('CustomPagesModule.base', 'Save'); ?>
            </button>
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= Yii::t('CustomPagesModule.base', 'Cancel'); ?></button>
        </div>
    <?php ActiveForm::end(); ?>
<?php humhub\widgets\ModalDialog::end() ?>
