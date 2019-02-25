<?php
use yii\helpers\Html;

$cssClass = ($page->hasAttribute('cssClass') && !empty($page->cssClass)) ? $page->cssClass :  'custom-pages-page';
?>
<div class="panel panel-default <?= Html::encode($cssClass) ?>">
    <div class="panel-body">
        <?= humhub\widgets\MarkdownView::widget(['markdown' => $md]); ?>
    </div>
</div>
