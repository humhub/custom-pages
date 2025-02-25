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
use humhub\widgets\Button;
use humhub\widgets\Label;

/* @var TemplateInstance $templateInstance */
/* @var BaseElementContent[] $elementContents */
/* @var string $sguid */
/* @var array $options */

Assets::register($this);
InlineEditorAsset::register($this);
?>
<?= Html::beginTag('ul', $options) ?>
    <li>
        <?= Label::warning(Yii::t('CustomPagesModule.template', 'Template')) ?>:
        <?= Template::getTypeTitle($templateInstance->template->type) ?>:
        <?= $templateInstance->template->name ?>
        <?= Label::success('#' . $templateInstance->id)->tooltip(Yii::t('CustomPagesModule.template', 'Template Instance Id')) ?>
        <?= Button::primary(Yii::t('CustomPagesModule.template', 'Edit all elements'))
            ->action('ui.modal.load', ['/custom_pages/template/element-content/edit-multiple', 'id' => $templateInstance->id, 'sguid' => $sguid])
            ->xs() ?>
        <ul>
            <?php foreach ($elementContents as $elementContent) : ?>
            <li>
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
            </li>
            <?php endforeach; ?>
        </ul>
    </li>
<?= Html::endTag('ul') ?>
