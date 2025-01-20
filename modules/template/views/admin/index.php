<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2016 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\modules\custom_pages\modules\template\models\Template;
use humhub\modules\custom_pages\modules\template\widgets\TemplateAdminMenu;
use humhub\modules\custom_pages\widgets\AdminMenu;
use humhub\widgets\Button;
use humhub\widgets\GridView;
use yii\helpers\Url;

?>
<div class="panel panel-default">
    <div class="panel-heading"><?php echo Yii::t('CustomPagesModule.base', '<strong>Custom</strong> Pages'); ?></div>
    <?= AdminMenu::widget([]); ?>
    <div class="panel-body">
        <h4><?= Yii::t('CustomPagesModule.base', 'Overview') ?></h4>
        <div class="help-block">
            <?= $helpText ?>
        </div>
    </div>

    <?= TemplateAdminMenu::widget(); ?>

    <div class="panel-body">
        <?= Button::success(Yii::t('CustomPagesModule.base', 'Create new {type}', ['type' => Template::getTypeTitle($type)]))
            ->link(['edit'])
            ->icon('plus')
            ->right()
            ->sm() ?>

        <?= Button::info(Yii::t('CustomPagesModule.base', 'Import {type}', ['type' => Template::getTypeTitle($type)]))
            ->action('ui.modal.load', ['import-source', 'type' => $type])
            ->icon('download')
            ->right()
            ->style('margin-right:5px')
            ->sm() ?>

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                [
                    'attribute' => 'id',
                    'options' => ['style' => 'width:40px;'],
                    'format' => 'raw',
                    'value' => function ($data) {
                        return $data->id;
                    },
                ],
                'name',
                [
                    'header' => Yii::t('CustomPagesModule.template', 'Actions'),
                    'class' => 'yii\grid\ActionColumn',
                    'options' => ['style' => 'width:80px; min-width:80px;'],
                    'template' => '{export} {update} {delete}',
                    'buttons' => [
                        'export' => function ($url, $model) {
                            return Button::defaultType()->icon('upload')
                                ->link(Url::toRoute(['export-source', 'id' => $model->id]))
                                ->tooltip(Yii::t('CustomPagesModule.template', 'Export {type}', ['type' => $model->type]))
                                ->loader(false)
                                ->xs();
                        },
                        'update' => function ($url, $model) {
                            return Button::primary()->icon('fa-pencil')
                                ->link(Url::toRoute(['edit-source', 'id' => $model->id]))
                                ->xs();
                        },
                        'delete' => function ($url, $model) {
                            return Button::danger()->icon('fa-times')
                                ->link(Url::toRoute(['delete-template', 'id' => $model->id]))
                                ->xs()
                                ->confirm();
                        },
                    ],
                ],
            ],
        ]) ?>
    </div>
</div>
