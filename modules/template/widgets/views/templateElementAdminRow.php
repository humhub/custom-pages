<?php

use humhub\widgets\Button;
use yii\helpers\Html;

/* @var $model humhub\modules\custom_pages\modules\template\models\TemplateElement */
?>
<tr data-template-element-definition="<?= $model->id ?>" >
    <td class="text-nowrap">
        #<strong><?= Html::encode($model->name) ?> </strong>
    </td>
    <td>
        <small>
            <span class="label label-success"><?= $model->getLabel() ?></span>
        </small>
        <?php if (!$model->hasDefaultContent()) : ?>
            <small>
                <span class="label label-warning"><?= Yii::t('CustomPagesModule.base', 'Empty') ?></span>
            </small>
        <?php else: ?>
            <small>
                <span class="label btn-success"><?= Yii::t('CustomPagesModule.base', 'Default') ?></span>
            </small>
        <?php endif; ?>
    </td>

    <td>
    <?php if ($model->template->canEdit()) : ?>
        <?= Button::primary()->icon('pencil')->xs()
            ->action('ui.modal.load', ['/custom_pages/template/admin/edit-element', 'id' => $model->id]) ?>
        <?= Button::danger()->icon('times')->xs()
            ->action('deleteElementSubmit', ['/custom_pages/template/admin/delete-element', 'id' => $model->id])
            ->confirm(
                Yii::t('CustomPagesModule.template', '<strong>Confirm</strong> element deletion'),
                Yii::t('CustomPagesModule.template', 'Do you really want to delete this element? <br />The deletion will affect all pages using this template.'),
                Yii::t('CustomPagesModule.base', 'Delete'),
            ) ?>
    <?php else : ?>
        <?= Button::info()->icon('eye')->xs()
            ->action('ui.modal.load', ['/custom_pages/template/admin/edit-element', 'id' => $model->id]) ?>
    <?php endif; ?>
    </td>
</tr>
