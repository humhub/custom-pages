<?php

use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\custom_pages\helpers\Url;

/* @var $pageId integer */
/* @var $contentContainer ContentContainerActiveRecord */
/* @var $target string */
/* @var $btnClass string */
/* @var $btnStyles string */
?>
<a href="<?= Url::toEditPage($pageId, $contentContainer) ?>"<?= empty($target) ? '' : ' target="' . $target. '"'?>
   class="<?= $btnClass ?>"<?= empty($btnStyles) ? '' : ' style="' . $btnStyles. '"'?>>
    <i class="fa fa-pencil"></i>
    <?= Yii::t('CustomPagesModule.base', 'Edit Page') ?>
</a>