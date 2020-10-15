<?php

use humhub\modules\custom_pages\widgets\CustomPageInlineStyle;
use yii\helpers\Html;
use humhub\modules\content\widgets\richtext\RichText;
/** @var $page \humhub\modules\custom_pages\models\Page */

$cssClass = ($page->hasAttribute('cssClass') && !empty($page->cssClass)) ? $page->cssClass :  'custom-pages-page';
?>

<?= CustomPageInlineStyle::widget(['theme' => $this->theme]); ?>

<div class="panel panel-default <?= Html::encode($cssClass) ?>">
    <div class="panel-body">
        <?= RichText::output($md)?>
    </div>
</div>
