<?php
/* @var $template humhub\modules\custom_pages\models\Template */
?>
<div class="grid-view">
    <table class="table table-hover">
        <colgroup>
            <col style="width:40px;">
            <col>
            <col style="width:80px; min-width:80px;">
        </colgroup>
        <tbody id="templateBlocks">
            <?php $blocks = $template->blocks ?>
        
            <?php if(count($blocks) > 0): ?>
                <?php foreach($blocks as $block):?>
                    <?=  humhub\modules\custom_pages\widgets\TemplateBlockAdminRow::widget(['model' => $block]); ?>
                <?php endforeach;?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
