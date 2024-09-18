<?php

use humhub\modules\custom_pages\modules\template\assets\SourceEditorAsset;
use humhub\modules\ui\form\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

SourceEditorAsset::register($this);

/* @var $model humhub\modules\custom_pages\modules\template\models\Template */

$this->registerJsConfig('custom_pages.template.source', [
    'text' => [
        'warning.beforeunload' => Yii::t('CustomPagesModule.modules_template_views_admin_editSource', "You haven't saved your last changes yet. Do you want to leave without saving?")
    ]
]);
?>
<div id="templatePageRoot" class="panel panel-default" data-ui-widget="custom_pages.template.source.TemplateSourceEditor" data-ui-init="1">
    <div class="panel-heading"><?= Yii::t('CustomPagesModule.base', '<strong>Custom</strong> Pages'); ?></div>

    <?= \humhub\modules\custom_pages\widgets\AdminMenu::widget([]); ?>

    <div class="panel-body">
        <?= Html::a('<i class="fa fa-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;' . Yii::t('CustomPagesModule.base', 'Back to overview'), Url::to(['index']), ['class' => 'btn btn-default pull-right', 'data-ui-loader' => '']); ?>
        <h4><?= Yii::t('CustomPagesModule.modules_template_views_admin_editSource', 'Edit template \'{templateName}\'', ['templateName' => Html::encode($model->name)]); ?></h4>
        <div class="help-block">
            <?=
            Yii::t('CustomPagesModule.modules_template_views_admin_editSource', 'Here you can edit the source of your template by defining the template layout and adding content elements. '
                    . 'Each element can be assigned with a default content and additional definitions.');
            ?>
        </div>
    </div>
    <a href="<?= Url::to(['preview', 'id' => $model->id]) ?>" id="previewButton" target="_blank" class="btn btn-success btn-sm pull-right" style="margin: 0 10px 10px 0">
                    <i aria-hidden="true" class="fa fa-eye"></i>
    </a>
    <ul class="nav nav-tabs tab-sub-menu" id="tabs">
        <li>
            <?= Html::a(Yii::t('CustomPagesModule.base', 'General'), Url::to(['edit', 'id' => $model->id])); ?>
        </li>
        <li class="active">
            <?= Html::a(Yii::t('CustomPagesModule.base', 'Source'), Url::to(['edit-source', 'id' => $model->id])); ?>
        </li>
        <li>
            <?= Html::a(Yii::t('CustomPagesModule.base', 'Usage'), Url::to(['edit-usage', 'id' => $model->id])); ?>
        </li>
        <!-- <li>
            <?// echo Html::a('<i aria-hidden="true" class="fa fa-question-circle"></i> '.Yii::t('CustomPagesModule.base', 'Help'), Url::to(['info', 'id' => $model->id])); ?>
        </li> -->
    </ul>

    <div class="panel-body">

        <?php $form = ActiveForm::begin(['enableClientValidation' => false, 'options' => ['id' => 'sourceForm']]); ?>

        <?= $form->field($model, 'source')->textarea([
            'id' => 'template-form-source',
            'spellcheck' => 'false',
            'inputClass' => 'form-control autosize',
            'rows' => 15
        ])->label(false); ?>

        <div class="clearfix">
            <?= Html::submitButton(Yii::t('CustomPagesModule.base', 'Save'), ['class' => 'btn btn-primary', 'data-ui-loader' => ""]); ?>
            <div class="dropdown pull-right">
                <button data-action-click="ui.modal.load" data-action-data-type="json" data-action-url="<?= Url::to(['/custom-pages/template/admin/edit-multiple', 'id' => $model->id]) ?>" class="btn btn-primary">
                    <i aria-hidden="true" class="fa fa-pencil"></i>
                    <?= Yii::t('CustomPagesModule.modules_template_views_admin_editSource', 'Edit All'); ?>
                </button>
                <button class="btn btn-success dropdown-toggle" type="button" data-toggle="dropdown">
                    <i aria-hidden="true" class="fa fa-plus"></i>
                    <?= Yii::t('CustomPagesModule.modules_template_views_admin_editSource', 'Add Element'); ?>
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" id="addElementSelect">
                    <?php foreach ($contentTypes as $label => $type) : ?>
                        <li>
                            <a data-action-click="ui.modal.load" data-action-data-type="json" data-action-url="<?= Url::to(['/custom-pages/template/admin/add-element', 'templateId' => $model->id, 'type' => $type]) ?>" href="#">
                                <?= $label ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <br />
        <?php ActiveForm::end(); ?>

        <?= \humhub\modules\custom_pages\modules\template\widgets\TemplateContentTable::widget(['template' => $model]) ?>
    </div>
</div>
