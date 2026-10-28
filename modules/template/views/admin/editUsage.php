<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\helpers\Html;
use humhub\modules\custom_pages\modules\template\models\Template;
use humhub\modules\custom_pages\modules\template\widgets\TemplateCard;
use humhub\widgets\GridView;
use yii\data\ActiveDataProvider;

/* @var Template $model */
/* @var ActiveDataProvider $dataProvider */
/* @var array $columns */
?>
<?php TemplateCard::begin([
    'model' => $model,
    'description' => Html::encode(Yii::t('CustomPagesModule.template', 'Here you can review where the template is used in.')),
]) ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'layout' => '{items}{pager}',
        'columns' => $columns,
    ]) ?>
<?php TemplateCard::end() ?>
