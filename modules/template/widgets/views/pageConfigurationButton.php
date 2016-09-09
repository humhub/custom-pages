<?php

use yii\helpers\Html;
use yii\helpers\Url;
?>

<a target="_blank" class="btn btn-danger btn-sm"  href="<?= Url::to(['edit', 'id' => $pageId, 'sguid' => $sguid]) ?>">
    <i class="fa fa-pencil"></i>
    <?= Yii::t('CustomPagesModule.base', 'Edit Page') ?>
</a>