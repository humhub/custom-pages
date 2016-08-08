<?php
/* @var $template humhub\modules\custom_pages\modules\template\models\Template */
?>
<div id="templateElementTable" class="grid-view" style="padding-top:0px;">
    <table class="table table-hover">
        <colgroup>
            <col style="width:40px;">
            <col>
            <col style="width:80px; min-width:80px;">
        </colgroup>
        <tbody id="templateElements">
            <?php $elements = $template->elements ?>
        
            <?php if(count($elements) > 0): ?>
                <?php foreach($elements as $element):?>
                    <?=  humhub\modules\custom_pages\modules\template\widgets\TemplateElementAdminRow::widget(['model' => $element, 'saved' => $saved]); ?>
                <?php endforeach;?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
