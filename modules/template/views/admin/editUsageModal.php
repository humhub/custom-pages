<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\modules\custom_pages\modules\template\models\Template;
use humhub\widgets\GridView;
use humhub\widgets\modal\Modal;
use humhub\widgets\modal\ModalButton;
use yii\data\ActiveDataProvider;

/* @var Template $model */
/* @var ActiveDataProvider $dataProvider */
/* @var array $columns */
?>
<?php Modal::beginDialog([
    'title' => Yii::t('CustomPagesModule.template', 'Usage'),
    'footer' => ModalButton::cancel(Yii::t('base', 'Close')),
]) ?>
    <strong><?= Template::getTypeTitle($model->type) ?>:</strong> <?= $model->name ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'layout' => '{items}{pager}',
        'columns' => $columns,
    ]) ?>
<?php Modal::endDialog() ?>
