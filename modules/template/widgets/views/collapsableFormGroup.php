<?php


$text = ($defaultState) ?  $closeText : $openText;
$contentStyle = ($defaultState) ? '' : 'display:none;';
?>

<hr class="hr-text collapsableTrigger" data-content="<?= $text ?>" />

<div class="collapsableContent" style="<?= $contentStyle ?>">
    <?= $content ?>
</div>

<script>
    $('.collapsableTrigger').off('click').on('click', function() {
        var $this = $(this);
        var $content = $this.next();
        $content.slideToggle('fast', function() {
            var text = ($content.is(":hidden")) ? '<?= $openText ?>' : '<?= $closeText ?>';
            $this.attr('data-content', text);
         });
    });
</script>
