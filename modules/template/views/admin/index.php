<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2016 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\helpers\Html;
use humhub\modules\custom_pages\modules\template\assets\TemplateAsset;
use humhub\modules\custom_pages\modules\template\components\TemplateActionColumn;
use humhub\modules\custom_pages\modules\template\models\Template;
use humhub\modules\custom_pages\modules\template\models\TemplateSearch;
use humhub\modules\custom_pages\widgets\AdminMenu;
use humhub\widgets\bootstrap\Button;
use humhub\widgets\form\ActiveForm;
use humhub\widgets\GridView;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;

/* @var ActiveDataProvider $dataProvider */
/* @var TemplateSearch $searchModel */

TemplateAsset::register($this);
?>
<div class="panel panel-default">
    <div class="panel-heading"><?= Yii::t('CustomPagesModule.base', '<strong>Custom</strong> Pages') ?></div>
    <?= AdminMenu::widget() ?>
    <div class="panel-body">
        <div class="text-body-secondary">
            <?= Yii::t('CustomPagesModule.base', 'Manage layouts, snippet layouts, and containers. Layouts define page structures, snippet layouts are used in sidebars or sections, and containers are reusable content blocks.') ?>
        </div>
    </div>

    <div class="panel-body">
        <div class="cp-templates-panel">
            <div class="cp-templates-filter">
                <?php $form = ActiveForm::begin(['method' => 'get']) ?>
                <div class="input-group">
                    <?= $form->field($searchModel, 'name')
                        ->textInput(['placeholder' => Yii::t('CustomPagesModule.template', 'Search by template ID or name')])
                        ->label(false) ?>
                    <?= Button::light()->icon('search')->submit() ?>
                </div>
                <?= $form->field($searchModel, 'type')
                    ->dropDownList(['' => Yii::t('CustomPagesModule.template', 'Type (All)')] + $searchModel->getTypeOptions())
                    ->label(false) ?>
                <script <?= Html::nonce() ?>>
                    $('#templatesearch-type').on('change', function () {this.form.submit()})
                </script>
                <?php ActiveForm::end() ?>
            </div>
            <div>
                <?= Button::info(Yii::t('CustomPagesModule.base', 'Import'))
                    ->action('ui.modal.load', ['import-source'])
                    ->icon('download')
                    ->style('margin-right:5px') ?>

                <?= Button::success(Yii::t('CustomPagesModule.base', 'Create'))
                    ->link(['edit'])
                    ->icon('plus') ?>
            </div>
        </div>

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                [
                    'attribute' => 'id',
                    'options' => ['style' => 'width:40px'],
                ],
                [
                    'attribute' => 'name',
                    'label' => Yii::t('CustomPagesModule.template', 'Name'),
                    'format' => 'raw',
                    'value' => function (Template $template) {
                        return Html::a($template->name, ['edit-source', 'id' => $template->id]);
                    },
                ],
                [
                    'label' => Yii::t('CustomPagesModule.template', 'Usage'),
                    'format' => 'raw',
                    'value' => function (Template $template) {
                        $count = $template->getLinkedRecordsQuery()->count();
                        return $count === 0 ? '0'
                            : Html::a($count, ['edit-usage', 'id' => $template->id], [
                                'data-action-click' => 'ui.modal.load',
                                'data-action-click-url' => Url::to(['edit-usage-modal', 'id' => $template->id]),
                            ]);
                    },
                ],
                [
                    'attribute' => 'type',
                    'label' => Yii::t('CustomPagesModule.template', 'Type'),
                    'format' => 'raw',
                    'value' => function (Template $template) {
                        return Html::tag('span', Template::getTypeTitle($template->type), [
                            'class' => 'badge badge-cp-template-' . $template->type,
                        ]);
                    },
                ],
                ['class' => TemplateActionColumn::class],
            ],
        ]) ?>
    </div>
</div>
