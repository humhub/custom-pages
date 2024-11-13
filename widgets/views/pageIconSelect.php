<?php

use humhub\libs\Html;
use humhub\modules\custom_pages\assets\Assets;
use humhub\modules\custom_pages\models\CustomPage;

/* @var array $faIcons */
/* @var CustomPage $page */

Assets::register($this);
?>

<div class="form-group">
    <label class="control-label" for="CustomPage[icon]"><?= $page->getAttributeLabel('icon') ?></label>

    <select class='selectpicker form-control' name="<?= $page->formName() ?>[icon]">
        <?php foreach ($faIcons as $name => $value): ?>

            <option class="" value="<?= $name; ?>" <?php if ($page->icon == $name): ?>selected='selected'<?php endif; ?>>
                <?= (substr($name, 0, 2) == 'fa') ?  substr($name, 3) : $name ?>
            </option>

        <?php endforeach; ?>
    </select>
</div>


<?= Html::script(<<<JS
    var formatState = function(state) {
        if (!state.id) {
            return state.text;
        }

        return $('<span><i class="fa '+state.element.value+'"></i> ' + state.text + '</span>');
    };

    $(".selectpicker").select2({
        theme: "humhub",
        templateResult: formatState,
        templateSelection: formatState
    });
JS
) ?>
