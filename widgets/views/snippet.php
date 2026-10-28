<?php

use humhub\widgets\Icon;
use yii\helpers\Html;

$snippetId = 'custom-snippet-'.$model->id;

$cssClass = ($model->hasAttribute('cssClass') && !empty($model->cssClass)) ? $model->cssClass :  'custom-pages-snippet';

$extraOptions = '';
foreach($navigation as $navigationOption) {
    $extraOptions .= '<li>'.$navigationOption.'</li>';
}

?>

<div class="panel panel-default custom-snippet <?= Html::encode($cssClass) ?> " id="<?= $snippetId ?>">
    <?php echo \humhub\widgets\PanelMenu::widget(['id' => $snippetId, 'extraMenus' => $extraOptions]); ?>
    <div class="panel-heading"><?= $model->icon ? Icon::get($model->icon) . ' ' : '' ?><?= Html::encode($model->title) ?></div>
    <div class="panel-body">
        <?= $content ?>
    </div>
</div>
