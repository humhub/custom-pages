<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\libs\Html;
use humhub\modules\custom_pages\assets\Assets;
use humhub\modules\custom_pages\modules\template\assets\InlineEditorAsset;
use humhub\modules\custom_pages\modules\template\elements\BaseElementContent;
use humhub\modules\custom_pages\modules\template\elements\ContainerItem;
use humhub\modules\custom_pages\modules\template\models\TemplateInstance;
use humhub\modules\custom_pages\modules\template\widgets\TemplateStructure;
use humhub\modules\ui\icon\widgets\Icon;
use humhub\modules\ui\view\components\View;
use humhub\widgets\Link;

/* @var TemplateInstance $templateInstance */
/* @var BaseElementContent[] $elementContents */
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
            <?php if ($templateInstance->isContainer()) : ?>
                <div class="cp-structure-actions dropdown">
                    <?= Icon::get('ellipsis-h', ['htmlOptions' => ['data-toggle' => 'dropdown']])
                        ->class('dropdown-toggle cp-structure-action') ?>
                    <ul class="dropdown-menu">
                        <li><?= Link::to(Yii::t('CustomPagesModule.template', 'Edit'))
                            ->icon('pencil')
                            ->action('editElements') ?></li>
                        <li><?= Link::to(Yii::t('CustomPagesModule.template', 'Move Up'))
                            ->icon('chevron-up')
                            ->action('moveUpContainerItem') ?></li>
                        <li><?= Link::to(Yii::t('CustomPagesModule.template', 'Move Down'))
                            ->icon('chevron-down')
                            ->action('moveDownContainerItem') ?></li>
                        <li><?= Link::to(Yii::t('CustomPagesModule.template', 'Delete'))
                            ->icon('trash')
                            ->action('deleteContainerItem')
                            ->confirm(
                                Yii::t('CustomPagesModule.template', '<strong>Confirm</strong> container item deletion'),
                                Yii::t('CustomPagesModule.template', 'Are you sure you want to delete this container item?'),
                                Yii::t('CustomPagesModule.base', 'Delete'),
                            ) ?></li>
                    </ul>
                </div>
            <?php else : ?>
                <?= Icon::get('pencil', ['htmlOptions' => ['data-action-click' => 'editElements']])
                    ->class('cp-structure-action') ?>
            <?php endif; ?>
        </div>

        <?php if (count($elementContents)) : ?>
        <?= Html::beginTag('ul') ?>
            <?php foreach ($elementContents as $elementContent) : ?>
            <?= Html::beginTag('li', $widget->getElementContentOptions($elementContent)) ?>
                <div class="cp-structure-container cp-structure-row" style="padding-left:<?= ($level + 1) * 10 + 8 ?>px">
                    <?= Icon::get('circle-o') ?>
                    <div class="cp-structure-text"><?= $elementContent->element->title === null || $elementContent->element->title === '' ? $elementContent->element->name : $elementContent->element->title ?></div>
                    <?php if ($elementContent->canAddItem()) : ?>
                        <?= Icon::get('plus', ['htmlOptions' => ['data-action-click' => 'addContainerItem']])
                            ->class('cp-structure-action') ?>
                    <?php endif; ?>
                </div>

                <?php if ($elementContent->hasItems()) : ?>
                    <?php foreach ($elementContent->items as $item) : ?>
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
