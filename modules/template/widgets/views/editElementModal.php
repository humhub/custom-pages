<?php

use humhub\compat\CActiveForm;
use yii\helpers\Html;

/* @var $model humhub\modules\custom_pages\modules\template\models\forms\TemplateElementForm */

?>
<div class="modal-dialog modal-dialog-large">
    <div class="modal-content">
        <?php $form = CActiveForm::begin(); ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">
                   <?= $title ?>
                </h4>
            </div>
            <div class="modal-body">
                <small class="pull-right">
                    <span class="label label-success"><?= $model->label ?></span>
                </small>
                <?= $form->field($model->element, 'name')->textInput(['readonly' => $model->scenario != 'create']); ?>
                
                <?php if($model->scenario != 'create') : ?>
                    <?= $form->field($model, 'use_default')->checkbox(); ?>
                <?php endif; ?>
                
                <?= $model->content->renderForm($form); ?>
                
                <?php foreach($model->fileList as $file) :?>
                     <?= Html::hiddenInput('fileList[]', $file); ?>
                <?php endforeach; ?>
                
            </div>
            <div class="modal-footer">
                <button id="editTemplateSubmit" class="btn btn-primary" data-ui-loader><?= Yii::t('CustomPagesModule.base', 'Save'); ?></button>
                <button type="button" class="btn btn-primary" data-dismiss="modal"><?php echo Yii::t('CustomPagesModule.base', 'Cancel'); ?></button>
            </div>
        <?php CActiveForm::end(); ?>
    </div>
</div>

<script type="text/javascript">
    $('#editTemplateSubmit').on('click', function (evt) {
        evt.preventDefault();
        
        var $form = $(this).closest('form');
        var action = $form.attr('action');

        $('textarea.ckeditorInput').each(function () {
            var $textarea = $(this);
            $textarea.val(CKEDITOR.instances[$textarea.attr('id')].getData());
         });

        $.ajax(action, {
            type: 'POST',
            dataType: 'json',
            data: $form.serialize(),
            success: function (json) {
                for(name in CKEDITOR.instances)
                {
                    CKEDITOR.instances[name].destroy(true);
                }
                if(json.success) {
                    $(document).trigger('templateElementEditSuccess', [json]);
                } else {
                    $('#globalModal').html(json.content);
                }
            },
            error: function () {
                $(document).trigger('templateElementEditError');
            }
        });
    });
</script>