<?php

use humhub\modules\custom_pages\widgets\OverviewSubMenu;
use humhub\modules\custom_pages\widgets\TargetPageList;

/* @var $targets \humhub\modules\custom_pages\models\Target[] */
/* @var $subNav string */
/* @var $pageType string */

\humhub\modules\custom_pages\assets\Assets::register($this);

?>
<div class="panel panel-default">
    <div class="panel-heading"><?= Yii::t('CustomPagesModule.base', '<strong>Custom</strong> Pages'); ?></div>

    <?= $subNav ?>

    <div class="panel-body">
        <div class="clearfix">
            <h4><?= Yii::t('CustomPagesModule.base', 'Overview') ?></h4>
            <div class="help-block">
                <?= Yii::t('CustomPagesModule.views_common_list', 'This page lists all available custom content entries.'); ?>
            </div>
        </div>
    </div>

    <?= OverviewSubMenu::widget() ?>

    <div class="panel-body">
        <?php foreach ($targets as $target) : ?>
            <?= TargetPageList::widget(['target' => $target, 'pageType' => $pageType])?>
        <?php endforeach; ?>
    </div>
</div>
