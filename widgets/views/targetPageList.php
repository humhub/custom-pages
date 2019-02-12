<?php

use yii\helpers\Html;
use humhub\widgets\GridView;
use yii\grid\DataColumn;
use yii\grid\ActionColumn;
use humhub\widgets\Link;
use humhub\modules\custom_pages\models\CustomContentContainer;
use humhub\modules\custom_pages\helpers\Url;
use humhub\widgets\Button;

/* @var $this \humhub\components\View */
/* @var $dataProvider \yii\data\ActiveDataProvider */
/* @var $target \humhub\modules\custom_pages\models\Target */
/* @var $pageTypelabel string */


?>

<h1 style="margin-top:15px;">
    <?= Html::encode($target->name) ?>
    <?= Button::success()->icon('fa-plus')->right()->link(Url::toCreatePage($target->id, $target->container))->xs()?>
</h1>

<?php if (!empty($pages)) : ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'class' => DataColumn::class,
                'label' => Yii::t('CustomPagesModule.base', 'Title'),
                'value' => function ($data) {
                    /*  @var $data CustomContentContainer */
                    return Link::to(Html::encode($data->getTitle()), $data->getEditUrl())->icon(Html::encode($data->getIcon()));
                }
            ],
            'navigation_class',
            'type',
            [
                'header' => 'Actions',
                'class' => ActionColumn::class,
                'options' => ['width' => '80px'],
                'buttons' => [
                    'update' => function ($url, $model) {
                        /*  @var $model CustomContentContainer */
                        return Link::primary()->icon('fa-pencil')->link($model->getEditUrl());
                    },
                    'view' => function () {
                        return;
                    },
                    'delete' => function () {
                        return;
                    },
                ],
            ],
        ]
    ]) ?>
<?php else: ?>
    <div class="alert alert-info" role="alert" style="margin-bottom:0">
        <?= Yii::t('CustomPagesModule.views_common_list', 'No {label} entry created yet!', ['label' => $pageTypelabel]); ?>
    </div>
<?php endif; ?>
