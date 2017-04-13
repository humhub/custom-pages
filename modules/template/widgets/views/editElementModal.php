<?php

use humhub\compat\CActiveForm;
use yii\helpers\Html;

/* @var $model humhub\modules\custom_pages\modules\template\models\forms\TemplateElementForm */

?>
<?php humhub\widgets\ModalDialog::begin(['header' => $title, 'size' => 'large']) ?>

    <?php $form = CActiveForm::begin(); ?>
        <div class="modal-body">
            <div class="clearfix">
                <?php if(!$model->element->isNewRecord) : ?>
                    #<strong><?= Html::encode($model->element->name) ?></strong>
                    <br />
                 <?php endif; ?>
                <small class="pull-right">
                    <span class="label label-success"><?= $model->label ?></span>
                </small>
            </div>

            <?php if($model->element->isNewRecord) : ?>
                <?= $form->field($model->element, 'name')->textInput(); ?>
            <?php else: ?>
                <div style="display:none">
                    <?= $form->field($model->element, 'name')->hiddenInput()->label(false); ?>
                </div>
            <?php endif; ?>

            <?= $form->field($model->element, 'title')->textInput(); ?>
            
            <?php if(false) : ?>
                <?= $form->field($model, 'use_default')->checkbox(['style' => 'margin: 100px']); ?>
            <?php endif; ?>

            <?= \humhub\modules\custom_pages\modules\template\widgets\EditContentSeperator::widget(['isAdminEdit' => $isAdminEdit])?>

            <?= $model->content->renderForm($form); ?>

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
    <?php CActiveForm::end(); ?>

<?php humhub\widgets\ModalDialog::end() ?>