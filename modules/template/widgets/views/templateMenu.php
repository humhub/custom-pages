<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */
use humhub\helpers\Html;
use humhub\modules\ui\menu\MenuLink;
use humhub\widgets\bootstrap\Button;

/* @var MenuLink[] $entries */
/* @var array $options */
?>
<?= Html::beginTag('div', $options) ?>
<?= Button::light()
    ->icon('ellipsis-h')
    ->options(['data-bs-toggle' => 'dropdown'])
    ->loader(false) ?>
<ul class="dropdown-menu">
    <?php foreach ($entries as $entry) : ?>
        <li>
            <?= $entry->render(['class' => 'dropdown-item']) ?>
        </li>
    <?php endforeach; ?>
</ul>
<?= Html::endTag('div') ?>
