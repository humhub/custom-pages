<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\helpers\Html;
use humhub\modules\custom_pages\models\CustomPage;

/* @var array $faIcons */
/* @var CustomPage $page */
?>
<div class="mb-3">
    <label class="control-label" for="CustomPage[icon]"><?= $page->getAttributeLabel('icon') ?></label>
    <select id="custom_page_icon" class="form-control" name="<?= $page->formName() ?>[icon]">
        <?php foreach ($faIcons as $icon) : ?>
            <option value="<?= $icon ?>"<?= $page->icon === $icon ? ' selected="selected"' : '' ?>>
                <?= str_starts_with($icon, 'fa-') ? substr($icon, 3) : $icon ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

<?= Html::script(<<<JS
    var formatState = function(state) {
        return state.id
            ? $('<span><i class="fa ' + state.element.value + '"></i> ' + state.text + '</span>')
            : state.text;
    };

    $("#custom_page_icon").select2({
        theme: "humhub",
        templateResult: formatState,
        templateSelection: formatState
    });
JS
) ?>
