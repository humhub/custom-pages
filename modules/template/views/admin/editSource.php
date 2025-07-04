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
use humhub\modules\ui\form\widgets\ActiveForm;
use humhub\widgets\Button;
use yii\helpers\Url;

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
            <?= $model->canEdit() ? Button::save()->submit() : '' ?>
            <?= $model->isNewRecord ? '' : '' ?>
            <?php if ($model->canEdit()) : ?>
            <div class="dropdown pull-right">
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
