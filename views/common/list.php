<?php

use humhub\components\View;
use humhub\modules\content\helpers\ContentContainerHelper;
use humhub\modules\custom_pages\widgets\PageTable;

/* @var $this View */
/* @var $pageType string */

?>
<?= PageTable::widget([
    'pageType' => $pageType,
    'container' => ContentContainerHelper::getCurrent(),
]) ?>
