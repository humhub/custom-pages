<?php

use humhub\widgets\bootstrap\Badge;
use humhub\widgets\bootstrap\Button;
use yii\helpers\Html;

/* @var $model humhub\modules\custom_pages\modules\template\models\TemplateElement */
?>
<tr data-template-element-definition="<?= $model->id ?>" >
    <td class="text-nowrap">
        #<strong><?= Html::encode($model->name) ?> </strong>
    </td>
    <td>
        <?= Badge::success($model->getLabel()) ?>
        <?php if (!$model->hasDefaultContent()) : ?>
            <?= Badge::warning(Yii::t('CustomPagesModule.base', 'Empty')) ?>
        <?php else: ?>
            <?= Badge::success(Yii::t('CustomPagesModule.base', 'Default')) ?>
        <?php endif; ?>
    </td>

    <td>
    <?php if ($model->template->canEdit()) : ?>
        <?= Button::primary()->icon('pencil')->sm()
            ->action('ui.modal.load', ['/custom_pages/template/admin/edit-element', 'id' => $model->id]) ?>
        <?= Button::danger()->icon('times')->sm()
            ->action('deleteElementSubmit', ['/custom_pages/template/admin/delete-element', 'id' => $model->id])
            ->confirm(
                Yii::t('CustomPagesModule.template', '<strong>Confirm</strong> element deletion'),
                Yii::t('CustomPagesModule.template', 'Do you really want to delete this element? <br />The deletion will affect all pages using this template.'),
                Yii::t('CustomPagesModule.base', 'Delete'),
            ) ?>
    <?php else : ?>
        <?= Button::accent()->icon('eye')->sm()
            ->action('ui.modal.load', ['/custom_pages/template/admin/edit-element', 'id' => $model->id]) ?>
    <?php endif; ?>
    </td>
</tr>
