<?php

use humhub\modules\ui\form\widgets\ActiveForm;
use humhub\modules\custom_pages\widgets\AdminMenu;
use yii\helpers\Html;
use yii\helpers\Url;
use humhub\modules\custom_pages\modules\template\models\Template;

?>
<div class="panel panel-default">
    <div class="panel-heading"><?= Yii::t('CustomPagesModule.base', '<strong>Custom</strong> Pages'); ?></div>
    <?= AdminMenu::widget(); ?>

    <div class="panel-body">
        <?= Html::a('<i class="fa fa-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;' . Yii::t('CustomPagesModule.base', 'Back to overview'), Url::to(['index']), array('class' => 'btn btn-default pull-right')); ?>
        <?php if ($model->isNewRecord): ?>
            <h4><?= Yii::t('CustomPagesModule.template', 'Create new {type}', ['type' => Template::getTypeTitle($model->type)]); ?></h4>
        <?php else: ?>
            <h4><?= Yii::t('CustomPagesModule.template', 'Edit template \'{templateName}\'', ['templateName' => Html::encode($model->name)]); ?></h4>
        <?php endif; ?>

    <?php if (!$model->isNewRecord): ?>
        </div>
        <ul class="nav nav-tabs tab-sub-menu" id="tabs">
            <li class="active">
                <?= Html::a(Yii::t('CustomPagesModule.base', 'General'), Url::to(['edit', 'id' => $model->id])); ?>
            </li>
            <li>
                <?= Html::a(Yii::t('CustomPagesModule.base', 'Source'), Url::to(['edit-source', 'id' => $model->id])); ?>
            </li>
            <li>
                <?= Html::a(Yii::t('CustomPagesModule.base', 'Usage'), Url::to(['edit-usage', 'id' => $model->id])); ?>
            </li>
        </ul>
        <div class="panel-body">
    <?php endif; ?>

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'name'); ?>
        <?= $form->field($model, 'description')->textarea(['id' => 'template-form-description', 'rows' => 3]); ?>

        <?php if ($model->isLayout()) : ?>
            <?= $form->field($model, 'allow_for_spaces')->checkbox(); ?>
        <?php endif; ?>

        <?= Html::submitButton(Yii::t('CustomPagesModule.template', 'Save'), ['class' => 'btn btn-primary', 'data-ui-loader' => '']); ?>

        <?php $form::end(); ?>
        <?= Html::script(' $(\'#template-form-description\').autosize();') ?>
    </div>
</div>
