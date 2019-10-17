<?php
use yii\helpers\Html;
use humhub\modules\content\widgets\richtext\RichText;

$cssClass = ($page->hasAttribute('cssClass') && !empty($page->cssClass)) ? $page->cssClass :  'custom-pages-page';
?>
<div class="panel panel-default <?= Html::encode($cssClass) ?>">
    <div class="panel-body">
        <?= RichText::output($md)?>
    </div>
</div>
