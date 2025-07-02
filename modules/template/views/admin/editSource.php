<?php

use humhub\modules\custom_pages\modules\template\assets\SourceEditorAsset;
use humhub\modules\custom_pages\modules\template\models\Template;
use humhub\modules\custom_pages\modules\template\services\ElementTypeService;
use humhub\modules\custom_pages\modules\template\widgets\TemplateContentTable;
use humhub\modules\custom_pages\widgets\AdminMenu;
use humhub\modules\ui\form\widgets\ActiveForm;
use humhub\widgets\Button;
use yii\helpers\Html;
use yii\helpers\Url;

SourceEditorAsset::register($this);

/* @var $model Template */

$elementTypeService = new ElementTypeService();

$this->registerJsConfig('custom_pages.template.source', [
    'text' => [
        'warning.beforeunload' => Yii::t('CustomPagesModule.template', "You haven't saved your last changes yet. Do you want to leave without saving?")
    ]
]);
?>
<div id="templatePageRoot" class="panel panel-default" data-ui-widget="custom_pages.template.source.TemplateSourceEditor" data-ui-init="1">
    <div class="panel-heading"><?= Yii::t('CustomPagesModule.base', '<strong>Custom</strong> Pages'); ?></div>

    <?= AdminMenu::widget(); ?>

    <div class="panel-body">
        <?= Button::defaultType(Yii::t('CustomPagesModule.base', 'Go Back'))
            ->icon('arrow-left')
            ->link(['index'])
            ->right() ?>

        <?php if ($model->canEdit()) : ?>
            <?= Yii::t('CustomPagesModule.template', '<strong>Editing:</strong> {templateName}', ['templateName' => Html::encode($model->name)]) ?>
        <?php else : ?>
            <?= Yii::t('CustomPagesModule.template', '<strong>Viewing:</strong> {templateName}', ['templateName' => Html::encode($model->name)]) ?>
        <?php endif; ?>

        <div class="help-block">
            <?= Yii::t('CustomPagesModule.template', 'Define template layouts and add content elements with default content and settings.') ?>
        </div>
    </div>

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
            <?= $model->canEdit() ? Button::save()->submit() : '' ?>
            <?= $model->isNewRecord ? '' : Button::defaultType(Yii::t('CustomPagesModule.template', 'Copy'))
                ->icon('copy')
                ->link(Url::toRoute(['copy', 'id' => $model->id])) ?>
            <?php if ($model->canEdit()) : ?>
            <div class="dropdown pull-right">
                <button data-action-click="ui.modal.load" data-action-data-type="json" data-action-url="<?= Url::to(['/custom_pages/template/admin/edit-multiple', 'id' => $model->id]) ?>" class="btn btn-primary">
                    <i aria-hidden="true" class="fa fa-pencil"></i>
                    <?= Yii::t('CustomPagesModule.template', 'Edit All'); ?>
                </button>
                <button class="btn btn-success dropdown-toggle" type="button" data-toggle="dropdown">
                    <i aria-hidden="true" class="fa fa-plus"></i>
                    <?= Yii::t('CustomPagesModule.template', 'Add Element'); ?>
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" id="addElementSelect">
                    <?php foreach ($elementTypeService->getTypeInstances() as $elementType) : ?>
                        <li>
                            <a data-action-click="ui.modal.load" data-action-data-type="json" data-action-url="<?= Url::to(['/custom_pages/template/admin/add-element', 'templateId' => $model->id, 'type' => get_class($elementType)]) ?>" href="#">
                                <?= $elementType->getLabel() ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
        </div>
        <br>

        <?php ActiveForm::end(); ?>

        <?= TemplateContentTable::widget(['template' => $model]) ?>
    </div>
</div>
