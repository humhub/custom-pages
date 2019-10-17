<?php

use humhub\compat\CActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use humhub\modules\custom_pages\widgets\AdminMenu;

?>
<div class="panel panel-default">
    <div class="panel-heading"><?= Yii::t('CustomPagesModule.base', '<strong>Custom</strong> Pages'); ?></div>
    <?= AdminMenu::widget([]); ?>

    <div class="panel-body">
        <?= Html::a('<i class="fa fa-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;' . Yii::t('CustomPagesModule.base', 'Back to overview'), Url::to(['index']), array('class' => 'btn btn-default pull-right')); ?>
        <?php if ($model->isNewRecord): ?>
            <h4><?= Yii::t('CustomPagesModule.views_admin_add', 'Create new template'); ?></h4>
        <?php else: ?>
            <h4><?= Yii::t('CustomPagesModule.views_admin_add', 'Edit template'); ?></h4>
        </div>
        <ul class="nav nav-tabs tab-sub-menu" id="tabs">
            <li class=" active">
                <?= Html::a(Yii::t('CustomPagesModule.base', 'General'), Url::to(['edit', 'id' => $model->id])); ?>
            <li class="">
                <?= Html::a(Yii::t('CustomPagesModule.base', 'Source'), Url::to(['edit-source', 'id' => $model->id])); ?>
        </ul>
        <div class="panel-body">
        <?php endif; ?>

        <?php $form = CActiveForm::begin(); ?>

            <?= $form->field($model, 'name'); ?>
            <?= $form->field($model, 'description')->textarea(['id' => 'template-form-description', 'rows' => 3, 'class' => 'autosize']); ?>

            <?php if($model->isLayout()) : ?>
                <?= $form->field($model, 'allow_for_spaces')->checkbox(); ?>
            <?php endif; ?>

            <?= Html::submitButton(Yii::t('CustomPagesModule.views_admin_edit', 'Save'), ['class' => 'btn btn-primary', 'data-ui-loader' => ""]); ?>

        <?php CActiveForm::end(); ?>

    </div>
</div>