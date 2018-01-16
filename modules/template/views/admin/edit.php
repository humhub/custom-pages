<?php

use humhub\compat\CActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use humhub\modules\custom_pages\modules\template\models\Template;
?>
<div class="panel panel-default">
    <div class="panel-heading"><?= Yii::t('CustomPagesModule.base', '<strong>Custom</strong> Pages'); ?></div>
    <?= \humhub\modules\custom_pages\widgets\AdminMenu::widget(); ?>

    <div class="panel-body">
        <?= Html::a('<i class="fa fa-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;' . Yii::t('CustomPagesModule.base', 'Back to overview'), Url::to(['index']), array('class' => 'btn btn-default pull-right')); ?>
        <?php if ($model->isNewRecord): ?>
            <h4><?= Yii::t('CustomPagesModule.modules_template_views_admin_edit', 'Create new {type}', ['type' => $model->type]); ?></h4>
        <?php else: ?>
            <h4><?= Yii::t('CustomPagesModule.modules_template_views_admin_edit', 'Edit template \'{templateName}\'', ['templateName' => Html::encode($model->name)]); ?></h4>
            </div>
            <ul class="nav nav-tabs tab-sub-menu" id="tabs">
                <li class=" active">
                    <?= Html::a(Yii::t('CustomPagesModule.base', 'General'), Url::to(['edit', 'id' => $model->id])); ?>
                </li>
                <li class="">
                    <?= Html::a(Yii::t('CustomPagesModule.base', 'Source'), Url::to(['edit-source', 'id' => $model->id])); ?>
                </li>
            </ul>
            <div class="panel-body">
        <?php endif; ?>

        <?php $form = CActiveForm::begin(); ?>

        <?= $form->field($model, 'name'); ?>
        <?= $form->field($model, 'description')->textarea(['id' => 'template-form-description', 'rows' => 3]); ?>
   
        <?php if($model->isLayout()) : ?>
            <?= $form->field($model, 'allow_for_spaces')->checkbox(); ?>
        <?php endif; ?>
        
        <?php if($model->type === Template::TYPE_CONTAINER) : ?>
            <?= $form->field($model, 'allow_inline_activation')->checkbox(); ?>
        <?php endif; ?>
        
        <?= Html::submitButton(Yii::t('CustomPagesModule.modules_template_views_admin_edit', 'Save'), ['class' => 'btn btn-primary', 'data-ui-loader' => ""]); ?>
            
        <?php CActiveForm::end(); ?>
        <script type="text/javascript">
            $('#template-form-description').autosize();
        </script>
    </div>
</div>