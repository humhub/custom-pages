<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\components\View;
use humhub\modules\custom_pages\widgets\AdminMenu;
use humhub\widgets\PageHeader;

/**
 * A space's Custom Pages management inside the space's layout: the page header with the tabs
 * Pages and Snippets, then the view's content cards.
 *
 * @var View $this
 * @var string $content
 */
?>
<?php $this->beginContent('@space/views/space/_layout.php') ?>
<div class="d-flex flex-column gap-3">
    <?= PageHeader::title(Yii::t('CustomPagesModule.base', 'Custom Pages'))->tabs(AdminMenu::class) ?>
    <?= $content ?>
</div>
<?php $this->endContent() ?>
