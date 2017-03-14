<?php

use yii\helpers\Html;

/* @var $model humhub\modules\custom_pages\modules\template\models\TemplateElement */

use yii\helpers\Url;
?>
<tr data-template-element-definition="<?= $model->id ?>" >
    <td>
        #<strong><?= Html::encode($model->name) ?> </strong>
    </td>
    <td>
        <small>
            <span class="label label-success"><?= $model->getLabel() ?></span>
        </small>
        <?php if (!$model->hasDefaultContent() || $model->defaultContent->use_default) : ?>
            <small>
                <span class="label label-warning"><?= Yii::t('CustomPagesModule.base', 'Empty') ?></span>
            </small>
        <?php else: ?>
            <small>
                <span class="label btn-success"><?= Yii::t('CustomPagesModule.base', 'Default') ?></span>
            </small>
        <?php endif; ?>
        <?php if ($saved) : ?> 
            <?= \humhub\widgets\DataSaved::widget() ?>
        <?php endif; ?>
    </td>

    <td>
        <a data-action-click="ui.modal.load" data-action-data-type="json" data-action-url="<?= Url::to(['/custom_pages/template/admin/edit-element', 'id' => $model->id]); ?>" class="btn btn-primary btn-xs tt" href="#">
            <i class="fa fa-pencil"></i>
        </a> 
        <a data-action-click="deleteElementSubmit" 
           data-action-url="<?= Url::to(['/custom_pages/template/admin/delete-element', 'id' => $model->id]); ?>"
           data-action-confirm="<?= Yii::t('CustomPagesModule.modules_template_widgets_views_confirmDeletionModal', 'Do you really want to delete this element? <br />The deletion will affect all pages using this template.') ?>" 
           data-action-confirm-header="<?= Yii::t('CustomPagesModule.modules_template_controller_OwnerContentController', '<strong>Confirm</strong> element deletion') ?>"
           data-action-confirm-text="<?= Yii::t('CustomPagesModule.base', 'Delete') ?>"
           class="btn btn-danger btn-xs tt" href="#">
            <i class="fa fa-times"></i>
        </a>
    </td>
</tr>
