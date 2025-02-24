<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\modules\custom_pages\modules\template\elements\BaseElementContent;
use humhub\modules\custom_pages\modules\template\models\Template;

/* @var Template $template */
/* @var BaseElementContent[] $elementContents */
?>
<ul>
    <li>
        Template: Layout: <?= $template->name ?>
        <ul>
            <?php foreach ($elementContents as $elementContent): ?>
            <li>
                <?= $elementContent->getLabel() ?> - <?= $elementContent->element->name ?>
            </li>
            <?php endforeach; ?>
        </ul>
    </li>
</ul>
