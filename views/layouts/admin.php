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
 * The administration's Custom Pages section: the page header with the section's tabs, then the
 * view's content cards.
 *
 * @var View $this
 * @var string $content
 */
?>
<?php $this->beginContent('@admin/views/layouts/main.php') ?>
<div class="d-flex flex-column gap-3">
    <?= PageHeader::title(Yii::t('CustomPagesModule.base', 'Custom Pages'))->tabs(AdminMenu::class) ?>
    <?= $content ?>
</div>
<?php $this->endContent() ?>
