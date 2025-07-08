<?php

use humhub\modules\custom_pages\modules\template\models\forms\TemplateElementForm;
use humhub\modules\custom_pages\modules\template\widgets\EditContentSeperator;
use humhub\modules\custom_pages\modules\template\widgets\TemplateContentFormFields;
use humhub\widgets\bootstrap\Button;
use humhub\widgets\form\ActiveForm;
use humhub\widgets\ModalDialog;
use yii\helpers\Html;

/* @var TemplateElementForm $model */
/* @var string $title */
/* @var bool $isAdminEdit */
/* @var string $resetUrl */
?>
<?php ModalDialog::begin(['header' => $title, 'size' => 'large']) ?>

    <?php $form = ActiveForm::begin(); ?>
        <div class="modal-body">
            <span class="label label-success pull-right"><?= $model->label ?></span>
            <?php if (!$model->element->isNewRecord) : ?>
                #<strong><?= Html::encode($model->element->name) ?></strong>
             <?php endif; ?>
            <div class="clearfix" style="margin-bottom:10px"></div>

            <?php if ($model->element->isNewRecord) : ?>
                <?= $form->field($model->element, 'name')->textInput(['autofocus' => '']); ?>
            <?php else: ?>
                <div style="display:none">
                    <?= $form->field($model->element, 'name')->hiddenInput()->label(false); ?>
                </div>
            <?php endif; ?>

            <?php if ($isAdminEdit) : ?>
                <?= $form->field($model->element, 'title')->textInput() ?>
            <?php endif; ?>

            <?= EditContentSeperator::widget(['isAdminEdit' => $isAdminEdit]) ?>

            <?= TemplateContentFormFields::widget(['form' => $form, 'model' => $model->content]) ?>

        </div>
        <div class="modal-footer">

            <?= $canEdit = $model->element->template->canEdit() ? Button::save()
                ->action('editElementSubmit')
                ->options(['data-action-target' => '#templatePageRoot'])
                ->submit() : '' ?>

            <?= Button::light(Yii::t('CustomPagesModule.base', 'Cancel'))
                ->options(['data-dismiss' => 'modal']) ?>

            <?php if ($canEdit && !$model->content->isNewRecord && $resetUrl != null) : ?>
                <?= Button::danger(Yii::t('CustomPagesModule.base', 'Reset'))
                    ->action('reset', $resetUrl)
                    ->options(['data-action-target' => '#templatePageRoot'])
                    ->right() ?>
            <?php endif; ?>

        </div>
    <?php $form::end(); ?>

<?php ModalDialog::end() ?>
