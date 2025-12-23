<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\modules\custom_pages\modules\template\assets\SourceEditorAsset;
use humhub\modules\custom_pages\modules\template\models\Template;
use humhub\modules\custom_pages\modules\template\services\ElementTypeService;
use humhub\modules\custom_pages\modules\template\widgets\TemplateContentTable;
use humhub\widgets\bootstrap\Button;
use humhub\widgets\form\ActiveForm;
use humhub\widgets\modal\ModalButton;

SourceEditorAsset::register($this);

/* @var $model Template */

$elementTypeService = new ElementTypeService();
?>
<div id="templatePageRoot" class="panel panel-default" data-ui-widget="custom_pages.template.source.TemplateSourceEditor" data-ui-init="1">
    <?= $this->render('editHeader', [
        'model' => $model,
        'description' => Yii::t('CustomPagesModule.template', 'Define template layouts and add content elements with default content and settings.'),
    ]) ?>

    <div class="panel-body">
        <?php $form = ActiveForm::begin(['enableClientValidation' => false, 'options' => ['id' => 'sourceForm']]); ?>

        <?= $form->field($model, 'source')->textarea([
            'id' => 'template-form-source',
            'spellcheck' => 'false',
            'inputClass' => 'form-control autosize',
            'rows' => 15,
        ])->label(false); ?>

        <div class="clearfix">
            <?php if ($model->canEdit()) : ?>
                <?= Button::save()->submit() ?>
                <?= ModalButton::success(Yii::t('CustomPagesModule.template', 'Add Element'))
                    ->icon('plus')
                    ->load(['/custom_pages/template/admin/select-element-type', 'templateId' => $model->id])
                    ->right()
                    ->loader(false) ?>
            <?php endif; ?>

            <?php if (!$model->isNewRecord && $model->is_default) : ?>
                <?= Button::light(Yii::t('CustomPagesModule.template', 'Copy'))
                    ->icon('copy')
                    ->link(['copy', 'id' => $model->id]) ?>
            <?php endif; ?>
        </div>
        <br>

        <?php ActiveForm::end(); ?>

        <?= TemplateContentTable::widget(['template' => $model]) ?>
    </div>
</div>
