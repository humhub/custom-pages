<?php


$text = ($defaultState) ?  $closeText : $openText;
$contentStyle = ($defaultState) ? '' : 'display:none;';
?>

<hr class="hr-text collapsableTrigger" data-content="<?= $text ?>" tabindex="0" />

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
    
    $('.collapsableTrigger').off('keyup').on('keyup', function(e) {
        switch (e.which) {
            case 13:
                e.preventDefault();
                $(this).trigger('click');
                break;
            case 39:
            case 40:
                e.preventDefault();
                if (!$(this).next('.panel-body').is(':visible')) {
                    $(this).trigger('click');
                }
                break;
            case 37:
            case 38:
                e.preventDefault();
                if ($(this).next('.panel-body').is(':visible')) {
                    $(this).trigger('click');
                }
                break;
        }
    });
</script>
