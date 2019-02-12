<?php

use yii\helpers\Url;
use yii\helpers\Html;
use humhub\modules\custom_pages\models\Page;
use humhub\modules\custom_pages\components\Container;
use humhub\modules\custom_pages\widgets\TargetPageList;

/* @var $targets \humhub\modules\custom_pages\models\Target[] */
/* @var $label string */
/* @var $subNav string */

?>
<div class="panel panel-default">
    <div class="panel-heading"><?= Yii::t('CustomPagesModule.base', '<strong>Custom</strong> Pages'); ?></div>
    <?= $subNav ?>
    <div class="panel-body">
        <div class="clearfix">
            <h4><?= Yii::t('CustomPagesModule.base', 'Overview') ?></h4>
            <div class="help-block">
                <?= Yii::t('CustomPagesModule.views_common_list', 'This page lists all available {label} entries.', ['label' => $label]); ?>
            </div>
        </div>

        <?php foreach ($targets as $target) : ?>
            <?= TargetPageList::widget(['target' => $target, 'pageTypeLabel' => $label])?>
        <?php endforeach; ?>

    </div>
</div>
