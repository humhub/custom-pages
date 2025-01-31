<?php

use humhub\modules\custom_pages\modules\template\models\forms\TemplateElementForm;
use humhub\modules\custom_pages\modules\template\widgets\EditContentSeperator;
use humhub\modules\custom_pages\modules\template\widgets\TemplateContentFormFields;
use humhub\modules\ui\form\widgets\ActiveForm;
use humhub\widgets\ModalDialog;
use yii\helpers\Html;

/* @var TemplateElementForm $model */
/* @var string $title */
/* @var bool $isAdminEdit */
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

            <?php if(!$model->content->isNewRecord && $resetUrl != null) : ?>
                <button class="btn btn-danger pull-left" style="background:transparent" ><?= Yii::t('CustomPagesModule.base', 'Reset'); ?></button>
            <?php endif; ?>

            <button type="submit" data-action-click="editElementSubmit" data-action-target="#templatePageRoot" data-ui-loader class="btn btn-primary">
                <?= Yii::t('CustomPagesModule.base', 'Save'); ?>
            </button>

            <button class="btn btn-default" data-dismiss="modal"><?= Yii::t('CustomPagesModule.base', 'Cancel'); ?></button>

            <?php if(!$model->content->isNewRecord && $resetUrl != null) : ?>
                <button data-action-click="reset" data-action-url="<?= $resetUrl ?>" data-action-target="#templatePageRoot"  class="btn btn-danger pull-right" data-ui-loader>
                    <?= Yii::t('CustomPagesModule.base', 'Reset'); ?>
                </button>
            <?php endif; ?>

        </div>
    <?php $form::end(); ?>

<?php ModalDialog::end() ?>
