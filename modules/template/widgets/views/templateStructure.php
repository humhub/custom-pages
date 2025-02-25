<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\modules\custom_pages\modules\template\elements\BaseElementContent;
use humhub\modules\custom_pages\modules\template\elements\ContainerElement;
use humhub\modules\custom_pages\modules\template\elements\ContainerItem;
use humhub\modules\custom_pages\modules\template\models\Template;
use humhub\modules\custom_pages\modules\template\models\TemplateInstance;
use humhub\modules\custom_pages\modules\template\widgets\TemplateStructure;
use humhub\widgets\Label;

/* @var TemplateInstance $templateInstance */
/* @var BaseElementContent[] $elementContents */
?>
<ul class="custom-pages-template-structure">
    <li>
        <?= Label::warning(Yii::t('CustomPagesModule.template', 'Template')) ?>:
        <?= Template::getTypeTitle($templateInstance->template->type) ?>:
        <?= $templateInstance->template->name ?>
        <?= Label::success('#' . $templateInstance->id)->tooltip(Yii::t('CustomPagesModule.template', 'Template Instance Id')) ?>
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
</ul>
