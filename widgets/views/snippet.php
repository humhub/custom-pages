<?php

use yii\helpers\Html;

$snippedId = 'custom-snippet-'.$model->id;

$extraOptions = '';
foreach($navigation as $navigationOption) {
    $extraOptions .= '<li>'.$navigationOption.'</li>';
}

?>

<div class="panel panel-default custom-snippet" id="<?= $snippedId ?>">
    <?php echo \humhub\widgets\PanelMenu::widget(['id' => $snippedId, 'extraMenus' => $extraOptions]); ?>
    <div class="panel-heading"><i class="fa <?= $model->icon; ?>"></i> <?= Html::encode($model->title) ?></div>
    <div class="panel-body">
        <?= $content ?>
    </div>
</div>