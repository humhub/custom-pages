<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2016 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\modules\custom_pages\assets\CodeMirrorAssetBundle;
use humhub\modules\custom_pages\modules\template\widgets\TemplateAdminMenu;
use humhub\modules\custom_pages\widgets\AdminMenu;
use humhub\widgets\Button;
use humhub\widgets\GridView;
use yii\helpers\Url;

// We preload the bundle here, so its immediately available on edit
CodeMirrorAssetBundle::register($this);
?>
<div class="panel panel-default">
    <div class="panel-heading"><?php echo Yii::t('CustomPagesModule.base', '<strong>Custom</strong> Pages'); ?></div>
    <?= AdminMenu::widget([]); ?>
    <div class="panel-body">
        <h4><?= Yii::t('CustomPagesModule.base', 'Overview', ['type' => $type]) ?></h4>
        <div class="help-block">
            <?= $helpText ?>
        </div>
    </div>

    <?= TemplateAdminMenu::widget(); ?>

    <div class="panel-body">
        <?= Button::success(Yii::t('CustomPagesModule.base', 'Create new {type}', ['type' => $type]))->icon('fa-plus')->right()->link(['edit'])->sm()?>


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
                    'header' => Yii::t('AdminModule.views_user_index', 'Actions'),
                    'class' => 'yii\grid\ActionColumn',
                    'options' => ['style' => 'width:80px; min-width:80px;'],
                    'buttons' => [
                        'view' => function ($url, $model) {
                            return null;
                        },
                        'update' => function ($url, $model) {
                            return Button::primary()->icon('fa-pencil')->link(Url::toRoute(['edit-source', 'id' => $model->id]))->xs();
                        },
                        'delete' => function ($url, $model) {
                            return Button::danger()->icon('fa-times')->link(Url::toRoute(['delete-template', 'id' => $model->id]))->xs()->confirm();
                        }
                    ],
                ],
            ],
        ]);
        ?>
    </div>
</div>