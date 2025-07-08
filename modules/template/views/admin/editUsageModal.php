<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\modules\custom_pages\modules\template\models\Template;
use humhub\widgets\GridView;
use humhub\widgets\modal\ModalButton;
use humhub\widgets\ModalDialog;
use yii\data\ActiveDataProvider;

/* @var Template $model */
/* @var ActiveDataProvider $dataProvider */
/* @var array $columns */
?>
<?php ModalDialog::begin(['header' => Yii::t('CustomPagesModule.template', 'Usage')]) ?>
<div class="modal-body">
    <strong><?= Template::getTypeTitle($model->type) ?>:</strong> <?= $model->name ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'layout' => '{items}{pager}',
        'columns' => $columns,
    ]) ?>
</div>
<div class="modal-footer">
    <?= ModalButton::cancel(Yii::t('base', 'Close')) ?>
</div>
<?php ModalDialog::end() ?>
