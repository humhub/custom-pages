<?php

use humhub\helpers\Html;

/* @var $this \humhub\components\View */
/* @var $defaultState boolean */
/* @var $closeText string */
/* @var $openText string */

$text = ($defaultState) ?  $closeText : $openText;
$contentStyle = ($defaultState) ? '' : 'display:none;';
?>

<hr class="hr-text collapsableTrigger" data-content="<?= $text ?>" tabindex="0" />

<div class="collapsableContent" style="<?= $contentStyle ?>">
    <?= $content ?>
</div>

<?= Html::script(<<<JS
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
JS
) ?>
