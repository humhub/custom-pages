<?php

use humhub\widgets\Icon;
use humhub\widgets\menu\MenuEntry;
use humhub\widgets\SidebarCard;
use yii\helpers\Html;

/* @var MenuEntry[] $navigation */

$snippetId = 'custom-snippet-'.$model->id;

$cssClass = ($model->hasAttribute('cssClass') && !empty($model->cssClass)) ? $model->cssClass :  'custom-pages-snippet';

?>
<?php SidebarCard::begin([
    'title' => ($model->icon ? Icon::get($model->icon) . ' ' : '') . Html::encode($model->title),
    'encodeTitle' => false,
    'menu' => $navigation,
    // Every snippet is rendered by the same widget: each needs a collapse id of its own
    'collapseId' => $snippetId . '-body',
    'options' => ['id' => $snippetId, 'class' => ['custom-snippet', $cssClass]],
]) ?>
        <?= $content ?>
<?php SidebarCard::end() ?>
