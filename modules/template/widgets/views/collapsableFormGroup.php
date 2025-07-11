<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\helpers\Html;

/* @var $defaultState boolean */
/* @var $closeText string */
/* @var $openText string */
/* @var $content string */
?>
<hr class="hr-text collapsableTrigger" data-content="<?= $defaultState ? $closeText : $openText ?>" tabindex="0">
<div class="collapsableContent<?= $defaultState ? '' : ' d-none'?>">
    <?= $content ?>
</div>

<script <?= Html::nonce() ?>>
$('.collapsableTrigger').off('click').on('click', function() {
    var $this = $(this);
    var $content = $this.next();
    $content.slideToggle('fast', function() {
        var text = ($content.is(":hidden")) ? '<?= $openText ?>' : '<?= $closeText ?>';
        $this.attr('data-content', text);
     });
}).off('keyup').on('keyup', function(e) {
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
