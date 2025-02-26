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
use humhub\modules\custom_pages\modules\template\elements\ContainerElement;
use humhub\modules\custom_pages\modules\template\elements\ContainerItem;
use humhub\modules\custom_pages\modules\template\models\Template;
use humhub\modules\custom_pages\modules\template\models\TemplateInstance;
use humhub\modules\custom_pages\modules\template\widgets\TemplateStructure;
use humhub\modules\ui\view\components\View;
use humhub\widgets\Button;
use humhub\widgets\Label;

/* @var TemplateInstance $templateInstance */
/* @var BaseElementContent[] $elementContents */
/* @var string $sguid */
/* @var array $options */
/* @var View $this */

/* @var TemplateStructure $widget */
$widget = $this->context;

Assets::register($this);
InlineEditorAsset::register($this);
?>
<?= Html::beginTag('ul', $options) ?>
    <?= Html::beginTag('li') ?>
        <?= Label::warning(Yii::t('CustomPagesModule.template', 'Template')) ?>
        <?= Template::getTypeTitle($templateInstance->template->type) ?>:
        <strong><?= $templateInstance->template->name ?></strong>
        <?= Label::success('#' . $templateInstance->id)->tooltip(Yii::t('CustomPagesModule.template', 'Template Instance Id')) ?>
        <?= Button::primary(Yii::t('CustomPagesModule.view', 'Edit elements'))
            ->icon('pencil')
            ->action('ui.modal.load', ['/custom_pages/template/element-content/edit-multiple', 'id' => $templateInstance->id, 'sguid' => $sguid])
            ->xs() ?>
        <?php if ($templateInstance->container_item_id !== null) : ?>
            <?= Button::danger()
                ->icon('times')
                ->action('deleteContainerItem', ['/custom_pages/template/container-content/delete-item', 'id' => $templateInstance->container_item_id, 'sguid' => $sguid])
                ->confirm(
                    Yii::t('CustomPagesModule.template', '<strong>Confirm</strong> container item deletion'),
                    Yii::t('CustomPagesModule.template', 'Are you sure you want to delete this container item?'),
                    Yii::t('CustomPagesModule.base', 'Delete'),
                )
                ->xs() ?>
        <?php endif; ?>

        <?= Html::beginTag('ul') ?>
            <?php foreach ($elementContents as $elementContent) : ?>
            <?= Html::beginTag('li', $widget->getElementContentOptions($elementContent)) ?>
                <?= Label::info($elementContent->getLabel()) ?>
                <?= empty($elementContent->element->title) ? '' : '- ' . $elementContent->element->title ?>
                - <code><?= $elementContent->element->name ?></code>

                <?php if ($elementContent instanceof ContainerElement) : ?>
                    - <?= Yii::t('CustomPagesModule.template', 'Multiple') ?>:
                    <?= $elementContent->definition->allow_multiple
                        ? Yii::t('CustomPagesModule.template', 'Yes')
                        : Yii::t('CustomPagesModule.template', 'No') ?>
                    <?= Button::success()
                        ->action('addContainerItem')
                        ->icon('plus')
                        ->xs() ?>

                    <?php if ($elementContent->hasItems()) : ?>
                        <?php foreach ($elementContent->items as $item) : ?>
                            <?php /* @var ContainerItem $item */ ?>
                            <?= TemplateStructure::widget(['templateInstance' => $item->templateInstance]) ?>
                        <?php endforeach; ?>
                    <?php endif; ?>

                <?php endif; ?>
            <?= Html::endTag('li') ?>
            <?php endforeach; ?>
        <?= Html::endTag('ul') ?>

    <?= Html::endTag('li') ?>
<?= Html::endTag('ul') ?>
