<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\helpers\Html;
use humhub\modules\custom_pages\modules\template\services\ElementTypeService;
use humhub\widgets\modal\Modal;
use humhub\widgets\modal\ModalButton;

/* @var int $templateId */

$elementTypeService = new ElementTypeService();
$typeGroups = [
    Yii::t('CustomPagesModule.template', 'Default') => $elementTypeService->getTypeInstances('default'),
    Yii::t('CustomPagesModule.template', 'Modules') => $elementTypeService->getTypeInstances('module'),
];
?>
<?php Modal::beginFormDialog([
    'title' => Yii::t('CustomPagesModule.template', 'Select new element type'),
    'footer' => ModalButton::cancel()
        . ModalButton::primary(Yii::t('CustomPagesModule.template', 'Next'))
            ->action('custom_pages.template.source.selectElementType', ['/custom_pages/template/admin/add-element','templateId' => $templateId])
            ->loader(false),
]) ?>

<?php foreach ($typeGroups as $title => $types) : ?>
    <?php if ($types !== []) : ?>
    <strong><?= $title ?></strong>
    <div class="row my-3 me-0" style="margin-left: -8px !important;">
        <?php foreach ($types as $type) : ?>
        <div class="col-md-4 ps-2 pe-0 pb-2">
            <label class="d-flex align-items-center border border-light rounded p-2 h-100">
                <?= Html::radio('type', false, ['value' => $type::class]) ?>
                <?= $type->getLabel() ?>
            </label>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
<?php endforeach; ?>

<?php Modal::endFormDialog() ?>
