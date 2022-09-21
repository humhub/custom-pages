<?php

use humhub\modules\content\widgets\richtext\RichText;
use humhub\modules\custom_pages\modules\template\widgets\PageConfigurationButton;
use humhub\modules\custom_pages\widgets\CustomPageInlineStyle;
use yii\helpers\Html;

/** @var $page \humhub\modules\custom_pages\models\Page */

$cssClass = ($page->hasAttribute('cssClass') && !empty($page->cssClass)) ? $page->cssClass : 'custom-pages-page';
?>

<?= CustomPageInlineStyle::widget(['theme' => $this->theme]); ?>

<?= PageConfigurationButton::widget() ?>
<div class="panel panel-default <?= Html::encode($cssClass) ?>">
    <div class="panel-body">
        <div class="markdown-render">
            <?= RichText::output($md) ?>
        </div>
    </div>
</div>
