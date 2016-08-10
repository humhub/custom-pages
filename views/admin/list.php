<?php

use yii\helpers\Html;
use humhub\modules\custom_pages\models\Page;
?>
<div class="panel panel-default">
    <div class="panel-heading"><?php echo Yii::t('CustomPagesModule.base', '<strong>Custom</strong> Pages'); ?></div>
    <?= \humhub\modules\custom_pages\widgets\AdminMenu::widget([]); ?>
    <div class="panel-body">
        <div class="clearfix">
            <?php echo Html::a('<i class="fa fa-plus"></i> ' . Yii::t('CustomPagesModule.base', 'Create new page'), ['add'], ['data-ui-loader' => '','class' => 'pull-right btn btn-success']); ?>
            <h4><?= Yii::t('CustomPagesModule.base', 'Overview') ?></h4>
            <div class="help-block">
                <?= Yii::t('CustomPagesModule.views_admin_list', 'This page lists all available main pages.'); ?>
            </div>
        </div>
        <br />
        <?php if (count($pages) != 0): ?>
            <?php
            $classes = Page::getNavigationClasses();
            $types = Page::getPageTypes();
            ?>
            <table class="table">
                <tr>
                    <th><?php echo Yii::t('CustomPagesModule.base', 'Title'); ?></th>
                    <th><?php echo Yii::t('CustomPagesModule.base', 'Navigation'); ?></th>
                    <th><?php echo Yii::t('CustomPagesModule.base', 'Type'); ?></th>
                    <th><?php echo Yii::t('CustomPagesModule.base', 'Sort Order'); ?></th>
                    <th>&nbsp;</th>
                </tr>
                <?php foreach ($pages as $page): ?>
                    <tr>
                        <td><i class="fa <?php echo $page->icon; ?>"></i> <?php echo Html::a($page->title, ['edit', 'id' => $page->id]); ?></td>
                        <td><?php echo $classes[$page->navigation_class]; ?></td>
                        <td><?php echo $types[$page->type]; ?></td>
                        <td><?php echo $page->sort_order; ?></td>
                        <td><?php echo Html::a('<i class="fa fa-pencil"></i>', ['edit', 'id' => $page->id], array('class' => 'btn btn-primary btn-xs pull-right')); ?></td>
                    </tr>

                <?php endforeach; ?>
            </table>

        <?php else: ?>

            <p><?php echo Yii::t('CustomPagesModule.base', 'No custom pages created yet!'); ?></p>


        <?php endif; ?>

    </div>
</div>


