<?php
    $cssClass = ($page->hasAttribute('cssClass') && !empty($page->cssClass)) ? $page->cssClass :  'custom-pages-page';
?>
<div class="panel panel-default <?= $cssClass ?>">
    <div class="panel-body">
        <?php echo humhub\widgets\MarkdownView::widget(['markdown' => $md]); ?>
    </div>
</div>
