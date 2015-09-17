<?php

use yii\helpers\Html;
use humhub\compat\CActiveForm;
?>
<div class="panel panel-default">
    <div class="panel-heading"><?php echo Yii::t('CustomPagesModule.views_admin_add', '<strong>Add</strong> new page'); ?></div>
    <div class="panel-body">

        <?php $form = CActiveForm::begin(); ?>

        <?php echo $form->errorSummary($model); ?>

        <div class="form-group">
            <?php echo $form->labelEx($model, 'type'); ?>
            <?php echo $form->dropdownList($model, 'type', $model->availableTypes, array('class' => 'form-control', 'rows' => '5', 'placeholder' => Yii::t('CustomPagesModule.views_admin_edit', 'Content'))); ?>
        </div>

        <?php echo Html::submitButton(Yii::t('CustomPagesModule.views_admin_edit', 'Next'), array('class' => 'btn btn-primary')); ?>

        <?php CActiveForm::end(); ?>

    </div>
</div>