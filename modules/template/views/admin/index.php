<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2016 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */
use yii\helpers\Html;
use humhub\widgets\GridView;
use yii\helpers\Url;
?>
<div class="panel panel-default">
    <div class="panel-heading"><?php echo Yii::t('CustomPagesModule.base', '<strong>Custom</strong> Pages'); ?></div>
    <?= \humhub\modules\custom_pages\widgets\AdminMenu::widget([]); ?>
    <div class="panel-body">
        <h4><?= Yii::t('CustomPagesModule.base', 'Overview', ['type' => $type]) ?></h4>
        <div class="help-block">
            <?= $helpText ?>
        </div>
    </div>
   
    <?= \humhub\modules\custom_pages\modules\template\widgets\TemplateAdminMenu::widget(); ?>
    
    <div class="panel-body">
        
         <?= Html::a('<i class="fa fa-plus"></i> ' . Yii::t('CustomPagesModule.base', 'Create new {type}', ['type' => $type]), ['edit'], ['class' => 'pull-right btn btn-success', 'data-ui-loader']); ?>
    
        
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                [
                    'attribute' => 'id',
                    'options' => ['style' => 'width:40px;'],
                    'format' => 'raw',
                    'value' => function($data) {
                return $data->id;
            },
                ],
                'name',
                [
                    'header' => Yii::t('AdminModule.views_user_index', 'Actions'),
                    'class' => 'yii\grid\ActionColumn',
                    'options' => ['style' => 'width:80px; min-width:80px;'],
                    'buttons' => [
                        'view' => function($url, $model) {
                            return null;
                        },
                        'update' => function($url, $model) {
                            return Html::a('<i class="fa fa-pencil"></i>', Url::toRoute(['edit-source', 'id' => $model->id]), ['class' => 'btn btn-primary btn-xs tt']);
                        },
                                'delete' => function($url, $model) {
                            return Html::a('<i class="fa fa-times"></i>', Url::toRoute(['delete-template', 'id' => $model->id]), ['class' => 'btn btn-danger btn-xs tt']);
                        }
                            ],
                        ],
                    ],
                ]);
                ?>
    </div>
</div>