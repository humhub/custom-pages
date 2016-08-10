<?php

use humhub\compat\CActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

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
                <div class="clearfix">
                    <?php if(!$model->element->isNewRecord) : ?>
                        #<strong><?= $model->element->name ?></strong>
                     <?php endif; ?>
                    <small class="pull-right">
                        <span class="label label-success"><?= $model->label ?></span>
                    </small>
                </div>
                
                <?php if($model->element->isNewRecord) : ?>
                    <?= $form->field($model->element, 'name')->textInput(); ?>
                <?php else: ?>
                    <div style="display:none">
                        <?= $form->field($model->element, 'name')->hiddenInput()->label(false); ?>
                    </div>
                <?php endif; ?>
                
                <?php if(false) : ?>
                    <?= $form->field($model, 'use_default')->checkbox(['style' => 'margin: 100px']); ?>
                <?php endif; ?>
                
                <?= \humhub\modules\custom_pages\modules\template\widgets\EditContentSeperator::widget(['isAdminEdit' => true])?>
                
                <?= $model->content->renderForm($form); ?>
                
                <?php foreach($model->fileList as $file) :?>
                     <?= Html::hiddenInput('fileList[]', $file); ?>
                <?php endforeach; ?>
                
            </div>
            <div class="modal-footer">
                <?php if(!$model->content->isNewRecord && $resetUrl != null) : ?>
                    <button type="button" class="btn btn-danger pull-left" style="background:transparent" ><?php echo Yii::t('CustomPagesModule.base', 'Reset'); ?></button>
                <?php endif; ?>
                <button id="editTemplateSubmit" class="btn btn-primary" data-ui-loader><?= Yii::t('CustomPagesModule.base', 'Save'); ?></button>
                <button type="button" class="btn btn-primary" data-dismiss="modal"><?php echo Yii::t('CustomPagesModule.base', 'Cancel'); ?></button>
                <?php if(!$model->content->isNewRecord && $resetUrl != null) : ?>
                    <button id="resetSubmit" type="button" class="btn btn-danger pull-right" data-ui-loader><?php echo Yii::t('CustomPagesModule.base', 'Reset'); ?></button>
                <?php endif; ?>
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
    
    <?php if(!$model->content->isNewRecord && $resetUrl != null) : ?>
    $('#resetSubmit').on('click', function() {
        $.ajax('<?= $resetUrl ?>', {
            type: 'POST',
            dataType: 'json',
            success: function (json) {
                $('#globalModal').modal('hide');
                $(document).trigger('contentResetSuccess', [json]);
            }
        });
    });
    <?php endif; ?>
</script>