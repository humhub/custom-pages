<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\helpers\Html;
use humhub\modules\custom_pages\modules\template\elements\ContainerElement;
use humhub\modules\custom_pages\modules\template\elements\ContainerItem;
use humhub\modules\ui\icon\widgets\Icon;
use humhub\widgets\bootstrap\Button;
use humhub\widgets\bootstrap\Link;

/* @var ContainerElement $container */
/* @var array $options */
?>
<?= Html::beginTag('div', $options) ?>
    <ul class="cp-container-items-list">
        <?php foreach ($container->items as $item) : ?>
            <?php /* @var ContainerItem $item */ ?>
            <li class="cp-container-item" data-item-id="<?= $item->id ?>" data-template-instance-id="<?= $item->templateInstance->id ?>">
                <?= Icon::get('circle')->class('cp-container-item-icon') ?>
                <span class="cp-container-item-name"><?= Html::encode($item->template->name) ?></span>
                <span class="cp-container-item-actions">
                    <?= Link::to()->icon('chevron-up')->action('moveUp')
                        ->title(Yii::t('CustomPagesModule.template', 'Move Up'))
                        ->cssClass('cp-container-item-move-up') ?>
                    <?= Link::to()->icon('chevron-down')->action('moveDown')
                        ->title(Yii::t('CustomPagesModule.template', 'Move Down'))
                        ->cssClass('cp-container-item-move-down') ?>
                    <?= Link::to()->icon('pencil')->action('editItem')
                        ->title(Yii::t('CustomPagesModule.template', 'Edit')) ?>
                    <?= Link::to()->icon('trash')->action('deleteItem')
                        ->confirm(
                            Yii::t('CustomPagesModule.template', '<strong>Confirm</strong> container item deletion'),
                            Yii::t('CustomPagesModule.template', 'Are you sure you want to delete this container item?'),
                            Yii::t('CustomPagesModule.base', 'Delete'),
                        )
                        ->title(Yii::t('CustomPagesModule.base', 'Delete')) ?>
                </span>
            </li>
        <?php endforeach; ?>
    </ul>
    <?= Button::light(Yii::t('CustomPagesModule.base', 'Add'))->icon('plus')->action('addItem')->sm()
        ->cssClass('cp-container-items-add' . ($container->canAddItem() ? '' : ' d-none')) ?>
<?= Html::endTag('div') ?>
