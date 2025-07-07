<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\modules\custom_pages\modules\template\models\TemplateElement;
use humhub\widgets\Button;
use humhub\widgets\Label;
use yii\helpers\Html;

/* @var $model TemplateElement */
?>
<tr data-template-element-definition="<?= $model->id ?>">
    <td>
        <?= Html::encode($model->title) ?>
    </td>
    <td class="text-nowrap">
        {{ <?= Html::encode($model->name) ?> }}
    </td>
    <td>
        <?= Label::success($model->getLabel()) ?>
        <?php if (!$model->hasDefaultContent()) : ?>
            <?= Label::warning(Yii::t('CustomPagesModule.base', 'Empty')) ?>
        <?php else : ?>
            <?= Label::success(Yii::t('CustomPagesModule.base', 'Default')) ?>
        <?php endif; ?>
    </td>
    <td>
    <?php if ($model->template->canEdit()) : ?>
        <?= Button::danger()->icon('times')->xs()
            ->action('deleteElementSubmit', ['/custom_pages/template/admin/delete-element', 'id' => $model->id])
            ->confirm(
                Yii::t('CustomPagesModule.template', '<strong>Confirm</strong> element deletion'),
                Yii::t('CustomPagesModule.template', 'Do you really want to delete this element? <br />The deletion will affect all pages using this template.'),
                Yii::t('CustomPagesModule.base', 'Delete'),
            ) ?>
        <?= Button::primary()->icon('pencil')->xs()
            ->action('ui.modal.load', ['/custom_pages/template/admin/edit-element', 'id' => $model->id]) ?>
    <?php else : ?>
        <?= Button::info()->icon('eye')->xs()
            ->action('ui.modal.load', ['/custom_pages/template/admin/edit-element', 'id' => $model->id]) ?>
    <?php endif; ?>
    </td>
</tr>
