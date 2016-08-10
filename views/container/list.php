<?php

use yii\helpers\Html;
use humhub\modules\custom_pages\models\ContainerPage;
?>
<div class="panel panel-default">
    <div class="panel-heading"><?php echo Yii::t('CustomPagesModule.base', '<strong>Custom</strong> Pages'); ?></div>
    <div class="panel-body">

        <div class="clearfix">
            <?php echo Html::a('<i class="fa fa-plus"></i> ' . Yii::t('CustomPagesModule.base', 'Create new page'), $container->createUrl('add'), ['class' => 'pull-right btn btn-success', 'data-ui-loader' => '']); ?>
            <h4><?= Yii::t('CustomPagesModule.base', 'Overview') ?></h4>
            <div class="help-block">
                <?= Yii::t('CustomPagesModule.views_container_list', 'This page lists all available pages of this space.'); ?>
            </div>
        </div>
        <p />
        <p />

        <?php if (count($pages) != 0): ?>
            <?php
            $types = ContainerPage::getPageTypes();
            ?>
            <table class="table">
                <tr>
                    <th><?php echo Yii::t('CustomPagesModule.base', 'Title'); ?></th>
                    <th><?php echo Yii::t('CustomPagesModule.base', 'Type'); ?></th>
                    <th><?php echo Yii::t('CustomPagesModule.base', 'Sort Order'); ?></th>
                    <th>&nbsp;</th>
                </tr>
                <?php foreach ($pages as $page): ?>
                    <tr>
                        <td><i class="fa <?php echo $page->icon; ?>"></i> <?php echo Html::a(Html::encode($page->title), $container->createUrl('edit', ['id' => $page->id])); ?></td>
                        <td><?php echo $types[$page->type]; ?></td>
                        <td><?php echo (int) $page->sort_order; ?></td>
                        <td><?php echo Html::a('<i class="fa fa-pencil"></i>', $container->createUrl('edit', ['id' => $page->id]), array('class' => 'btn btn-primary btn-xs pull-right')); ?></td>
                    </tr>

                <?php endforeach; ?>
            </table>

        <?php else: ?>

            <p><?php echo Yii::t('CustomPagesModule.base', 'No custom pages created yet!'); ?></p>


        <?php endif; ?>

    </div>
</div>


