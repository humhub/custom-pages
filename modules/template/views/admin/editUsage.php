<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\modules\custom_pages\helpers\Url;
use humhub\modules\custom_pages\modules\template\models\Template;
use humhub\modules\custom_pages\widgets\AdminMenu;
use humhub\widgets\Button;
use humhub\widgets\GridView;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;

/* @var Template $model */
/* @var ActiveDataProvider $dataProvider */
/* @var array $columns */
?>
<div class="panel panel-default">
    <div class="panel-heading"><?= Yii::t('CustomPagesModule.base', '<strong>Custom</strong> Pages') ?></div>

    <?= AdminMenu::widget() ?>

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
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'layout' => '{items}{pager}',
            'columns' => $columns,
        ]) ?>
    </div>
</div>
