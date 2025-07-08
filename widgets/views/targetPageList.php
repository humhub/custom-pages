<?php

use humhub\modules\custom_pages\helpers\Url;
use humhub\modules\custom_pages\models\CustomPage;
use humhub\widgets\bootstrap\Button;
use humhub\widgets\bootstrap\Link;
use humhub\widgets\GridView;
use humhub\widgets\modal\ModalButton;
use yii\grid\ActionColumn;
use yii\grid\DataColumn;
use yii\helpers\Html;

/* @var $this \humhub\components\View */
/* @var $dataProvider \yii\data\ActiveDataProvider */
/* @var $target \humhub\modules\custom_pages\models\Target */
/* @var $pageType string */

?>

<div class="target-page-list <?= Html::encode($target->id) ?>">
    <div class="target-page-list-head">
        <strong><?= $target->icon ? '<i class="fa ' . Html::encode($target->icon) . '"></i> ' : '' ?><?= Html::encode($target->name) ?></strong>
        <?= Button::success()->icon('plus')->right()->link(Url::toChooseContentType($target, $pageType))->sm() ?>
    </div>
    <div class="target-page-list-grid">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'layout' => '{items}{pager}',
            'columns' => [
                [
                    'class' => DataColumn::class,
                    'label' => Yii::t('CustomPagesModule.base', 'Title'),
                    'format' => 'raw',
                    'value' => function (CustomPage $data) {
                        return Link::to(Html::encode($data->getTitle()), $data->getUrl())->icon(Html::encode($data->icon));
                    },
                ],
                [
                    'class' => DataColumn::class,
                    'label' => Yii::t('CustomPagesModule.base', 'Type'),
                    'headerOptions' => ['style' => 'width:10%'],
                    'value' => function (CustomPage $data) {
                        return $data->getContentType()->getLabel();
                    },
                ],
                [
                    'class' => ActionColumn::class,
                    'options' => ['width' => '80px'],
                    'contentOptions' => ['class' => 'text-right'],
                    'template' => '{update} {copy}',
                    'buttons' => [
                        'update' => function ($url, CustomPage $model) {
                            return $model->canEdit()
                                ? Link::primary()->icon('pencil')->link($model->getEditUrl())->sm()
                                : '';
                        },
                        'copy' => function ($url, CustomPage $model) {
                            return ModalButton::light()->load(Url::toCopyPage($model))->icon('copy')->sm();
                        },
                    ],
                ],
            ],
        ]) ?>
    </div>
</div>
