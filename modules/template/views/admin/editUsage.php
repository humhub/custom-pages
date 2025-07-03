<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\modules\custom_pages\modules\template\models\Template;
use humhub\widgets\GridView;
use yii\data\ActiveDataProvider;

/* @var Template $model */
/* @var ActiveDataProvider $dataProvider */
/* @var array $columns */
?>
<div class="panel panel-default">
    <?= $this->render('editHeader', [
        'model' => $model,
        'description' => Yii::t('CustomPagesModule.template', 'Here you can review where the template is used in.'),
    ]) ?>

    <div class="panel-body">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'layout' => '{items}{pager}',
            'columns' => $columns,
        ]) ?>
    </div>
</div>
