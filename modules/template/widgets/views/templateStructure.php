<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\libs\Html;
use humhub\modules\custom_pages\assets\Assets;
use humhub\modules\custom_pages\modules\template\assets\InlineEditorAsset;
use humhub\modules\custom_pages\modules\template\elements\ContainerElement;
use humhub\modules\custom_pages\modules\template\elements\ContainerItem;
use humhub\modules\custom_pages\modules\template\models\TemplateInstance;
use humhub\modules\custom_pages\modules\template\widgets\TemplateStructure;
use humhub\modules\ui\icon\widgets\Icon;
use humhub\modules\ui\view\components\View;
use humhub\widgets\Link;

/* @var TemplateInstance $templateInstance */
/* @var ContainerElement[] $containers */
/* @var array $options */
/* @var array $templateInstanceOptions */
/* @var int $level */
/* @var View $this */

/* @var TemplateStructure $widget */
$widget = $this->context;

Assets::register($this);
InlineEditorAsset::register($this);
?>
<?php if ($templateInstance->isPage()) : ?>
<?= Html::beginTag('div', $options) ?>
    <div class="cp-structure-header cp-structure-row">
        <div class="cp-structure-text"><?= Yii::t('CustomPagesModule.template', 'Structure View') ?></div>
        <?= Icon::get('arrows') ?>
    </div>
<?php endif; ?>

<?= Html::beginTag('ul', $templateInstanceOptions) ?>
    <?= Html::beginTag('li') ?>
        <div class="cp-structure-template cp-structure-row" style="padding-left:<?= $level * 10 + 8 ?>px">
            <?= Icon::get('circle') ?>
            <div class="cp-structure-text"><?= $templateInstance->template->name ?></div>
            <div class="cp-structure-actions dropdown">
                <?= Icon::get('ellipsis-h', ['htmlOptions' => ['data-toggle' => 'dropdown']])
                    ->class('dropdown-toggle cp-structure-action') ?>
                <ul class="dropdown-menu dropdown-menu-right">
                <?php if ($templateInstance->isContainer()) : ?>
                    <li><?= Link::to(Yii::t('CustomPagesModule.template', 'Edit'))
                        ->icon('pencil')
                        ->action('editElements') ?></li>
                    <li><?= Link::to(Yii::t('CustomPagesModule.template', 'Move Up'))
                        ->icon('chevron-up')
                        ->action('moveUpContainerItem') ?></li>
                    <li><?= Link::to(Yii::t('CustomPagesModule.template', 'Move Down'))
                        ->icon('chevron-down')
                        ->action('moveDownContainerItem') ?></li>
                    <li><?= Link::to(Yii::t('CustomPagesModule.template', 'Export'))
                            ->icon('upload')
                            ->action('exportTemplateInstance') ?></li>
                    <li><?= Link::to(Yii::t('CustomPagesModule.template', 'Delete'))
                        ->icon('trash')
                        ->action('deleteContainerItem')
                        ->confirm(
                            Yii::t('CustomPagesModule.template', '<strong>Confirm</strong> container item deletion'),
                            Yii::t('CustomPagesModule.template', 'Are you sure you want to delete this container item?'),
                            Yii::t('CustomPagesModule.base', 'Delete'),
                        ) ?></li>
                <?php else : ?>
                    <li><?= Link::to(Yii::t('CustomPagesModule.template', 'Edit'))
                        ->icon('pencil')
                        ->action('editElements') ?></li>
                    <li><?= Link::to(Yii::t('CustomPagesModule.template', 'Export'))
                        ->icon('upload')
                        ->action('exportTemplateInstance') ?></li>
                    <li><?= Link::to(Yii::t('CustomPagesModule.template', 'Import'))
                            ->icon('download')
                            ->action('importTemplateInstance') ?></li>
                <?php endif; ?>
                </ul>
            </div>
        </div>

        <?php if (count($containers)) : ?>
        <?= Html::beginTag('ul') ?>
            <?php foreach ($containers as $container) : ?>
            <?= Html::beginTag('li', $widget->getContainerOptions($container)) ?>
                <div class="cp-structure-container cp-structure-row" style="padding-left:<?= ($level + 1) * 10 + 8 ?>px">
                    <?= Icon::get('circle-o') ?>
                    <div class="cp-structure-text"><?= $container->element->title === null || $container->element->title === '' ? $container->element->name : $container->element->title ?></div>

                    <div class="cp-structure-actions dropdown">
                        <?= Icon::get('ellipsis-h', ['htmlOptions' => ['data-toggle' => 'dropdown']])
                            ->class('dropdown-toggle cp-structure-action') ?>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <li><?= Link::to(Yii::t('CustomPagesModule.template', 'Import'))
                                    ->icon('download')
                                    ->action('importTemplateInstance') ?></li>
                            <li><?= Link::to(Yii::t('CustomPagesModule.template', 'Export'))
                                    ->icon('upload')
                                    ->action('exportTemplateInstance') ?></li>
                        </ul>
                    </div>
                    <?= Icon::get('plus', ['htmlOptions' => ['data-action-click' => 'addContainerItem']])
                        ->class('cp-structure-action')
                        ->style($container->canAddItem() ? '' : 'display:none') ?>
                </div>

                <?php if ($container->hasItems()) : ?>
                    <?php foreach ($container->items as $item) : ?>
                        <?php /* @var ContainerItem $item */ ?>
                        <?= TemplateStructure::widget([
                            'templateInstance' => $item->templateInstance,
                            'level' => $level + 2,
                        ]) ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            <?= Html::endTag('li') ?>
            <?php endforeach; ?>
        <?= Html::endTag('ul') ?>
        <?php endif; ?>

    <?= Html::endTag('li') ?>
<?= Html::endTag('ul') ?>

<?= $templateInstance->isPage() ? Html::endTag('div') : '' ?>
