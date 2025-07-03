<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */
use humhub\libs\Html;
use humhub\modules\ui\menu\MenuLink;
use humhub\widgets\Button;

/* @var MenuLink[] $entries */
/* @var array $options */
?>
<?= Html::beginTag('div', $options) ?>
<?= Button::defaultType()
    ->icon('ellipsis-h')
    ->cssClass('dropdown-toggle')
    ->options(['data-toggle' => 'dropdown'])
    ->loader(false) ?>
<ul class="dropdown-menu pull-right">
    <?php foreach ($entries as $entry) : ?>
        <li>
            <?= $entry->render() ?>
        </li>
    <?php endforeach; ?>
</ul>
<?= Html::endTag('div') ?>
