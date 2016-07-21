<?php

use humhub\compat\CActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $model humhub\modules\custom_pages\models\forms\TemplateBlockForm */

?>
<div class="modal-dialog modal-dialog-normal">
    <div class="modal-content">
        <?php $form = CActiveForm::begin(['action' => $model->action]); ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">
                    <?php if ($model->isNewRecord) : ?>
                        <?= Yii::t('CustomPagesModule.base', 'Add new {type} block', ['type' => $model->getTypeLabel()]); ?>
                    <?php else: ?>
                        <?= Yii::t('CustomPagesModule.base', 'Edit {type} block', ['type' => $model->getTypeLabel()]); ?>
                    <?php endif; ?>
                </h4>
            </div>
            <div class="modal-body">
                <?= $form->field($model->templateBlock, 'name')->textInput(['readonly' => !$model->isNewRecord]); ?>
                <?= $model->contentEditForm($form); ?>
            </div>
            <div class="modal-footer">
                <button id="editTemplateSubmit" class="btn btn-primary" data-ui-loader=""><?= Yii::t('CustomPagesModule.base', 'Save'); ?></button>
                <button type="button" class="btn btn-primary"
                        data-dismiss="modal"><?php echo Yii::t('AdminModule.views_module_setAsDefault', 'Close'); ?></button>
            </div>
        <?php CActiveForm::end(); ?>
    </div>
</div>
<script type="text/javascript">
    $('#editTemplateSubmit').on('click', function (evt) {
        evt.preventDefault();
        
        var $form = $(this).closest('form');
        var action = $form.attr('action');
        var data = $form.serialize();
     
        var type = $(this).data('content-type');
        $.ajax(action, {
            type: 'POST',
            dataType: 'json',
            data: data,
            success: function (json) {
                if(json.success) {
                    $(document).trigger('templateBlockEditSuccess', [json]);
                } else {
                    $('#globalModal').html(json.content);
                }
                
            },
            error: function () {
                $(document).trigger('templateBlockEditError');
            }
        });
    });
</script>