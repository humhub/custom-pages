<?php

use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\content\models\Content;
use humhub\modules\content\widgets\StateBadge;
use humhub\modules\custom_pages\helpers\Url;
use humhub\modules\custom_pages\models\CustomContentContainer;
use humhub\modules\custom_pages\modules\template\models\Template;
use humhub\modules\custom_pages\widgets\AdminMenu;
use humhub\widgets\GridView;
use humhub\widgets\Link;
use yii\data\ActiveDataProvider;
use yii\grid\ActionColumn;
use yii\grid\DataColumn;
use yii\helpers\Html;

/* @var Template $model */
/* @var ActiveDataProvider $dataProvider */

$columnLabel = $model->type === Template::TYPE_CONTAINER ? Yii::t('CustomPagesModule.base', 'Template')
    : ($model->type === Template::TYPE_SNIPPED_LAYOUT ? Yii::t('CustomPagesModule.base', 'Snippet')
    : Yii::t('CustomPagesModule.base', 'Page'));
?>
<div class="panel panel-default">
    <div class="panel-heading"><?= Yii::t('CustomPagesModule.base', '<strong>Custom</strong> Pages') ?></div>

    <?= AdminMenu::widget() ?>

    <div class="panel-body">
        <?= Html::a('<i class="fa fa-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;' . Yii::t('CustomPagesModule.base', 'Back to overview'), Url::to(['index']), ['class' => 'btn btn-default pull-right', 'data-ui-loader' => '']); ?>
        <h4><?= Yii::t('CustomPagesModule.modules_template_views_admin_editSource', 'Edit template \'{templateName}\'', ['templateName' => Html::encode($model->name)]); ?></h4>
        <div class="help-block">
            <?= Yii::t('CustomPagesModule.base', 'Here you can review where the template is used in.') ?>
        </div>
    </div>

    <ul class="nav nav-tabs tab-sub-menu" id="tabs">
        <li>
            <?= Html::a(Yii::t('CustomPagesModule.base', 'General'), Url::to(['edit', 'id' => $model->id])); ?>
        </li>
        <li>
            <?= Html::a(Yii::t('CustomPagesModule.base', 'Source'), Url::to(['edit-source', 'id' => $model->id])); ?>
        </li>
        <li class="active">
            <?= Html::a(Yii::t('CustomPagesModule.base', 'Usage'), Url::to(['edit-usage', 'id' => $model->id])); ?>
        </li>
    </ul>

    <div class="panel-body">
        <?php
        $columns = [
            [
                'class' => DataColumn::class,
                'label' => $columnLabel,
                'format' => 'raw',
                'value' => function ($model) {
                    if ($model instanceof Content) {
                        /* @var $record CustomContentContainer */
                        $record = $model->getPolymorphicRelation();
                        return Link::to(Html::encode($record->getTitle()), $record->getUrl())->icon(Html::encode($record->icon)) . ' ' .
                            StateBadge::widget(['model' => $record]);
                    } else if ($model instanceof Template) {
                        return $model->name;
                    }
                }
            ]
        ];
        if ($model->type !== Template::TYPE_CONTAINER) {
            $columns[] = [
                'class' => DataColumn::class,
                'label' => Yii::t('CustomPagesModule.base', 'Space'),
                'format' => 'raw',
                'value' => function ($model) {
                    return $model instanceof Content && $model->container instanceof ContentContainerActiveRecord
                        ? \humhub\libs\Html::containerLink($model->container)
                        : '';
                }
            ];
        }
        $columns[] = [
            'class' => ActionColumn::class,
            'options' => ['width' => '80px'],
            'buttons' => [
                'update' => function ($url, $model) {
                    if ($model instanceof Content) {
                        /* @var $record CustomContentContainer */
                        $record = $model->getPolymorphicRelation();
                        return $record->canEdit()
                            ? Link::primary()->icon('fa-pencil')->link($record->getEditUrl())->xs()->right()
                            : '';
                    } else if ($model instanceof Template) {
                        return Link::primary()->icon('fa-pencil')->link(Url::toRoute(['edit-source', 'id' => $model->id]))->xs();
                    }
                },
                'view' => function () {
                    return '';
                },
                'delete' => function ($url, $model) {
                    if ($model instanceof Template) {
                        return Link::danger()->icon('fa-times')->link(Url::toRoute(['delete-template', 'id' => $model->id]))->xs()->confirm();
                    }
                },
            ],
        ];
        echo GridView::widget([
            'dataProvider' => $dataProvider,
            'layout' => '{items}{pager}',
            'columns' => $columns
        ]) ?>
    </div>
</div>
