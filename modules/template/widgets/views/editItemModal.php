<?php

use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $model humhub\modules\custom_pages\modules\template\models\forms\TemplateElementForm */

$action = ($action == null) ? Url::to() : $action;

?>
<div class="modal-dialog modal-dialog-large">
    <div class="modal-content media">
        <?php $form = ActiveForm::begin(['action' => $action, 'enableClientValidation' => false]);?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">
                   <?= $title ?>
                </h4>
            </div>
            <div class="modal-body media-body">  
                <?= $form->field($model, 'title'); ?>
                <?php foreach($model->contentMap as $key => $contentItem) :?>
                    <h4 class="media-heading clearfix">
                        <strong>#<?= $contentItem->ownerContent->element_name ?></strong>
                        <small class="pull-right">
                            <span class="label label-success"><?= $contentItem->ownerContent->label ?></span>
                        </small>
                        <?php if($contentItem->content->isNewRecord): ?>
                            <small class="pull-right" style="margin-right: 2px">
                                <span class="label label-warning"><?= Yii::t('CustomPagesModule.widgets_views_editMultipleElements', 'Empty') ?></span>
                            </small>
                        <?php endif;?>
                    </h4> 
                    
                    <?= $contentItem->content->renderForm($form); ?>

                    <hr>
                <?php endforeach; ?>
                
            </div>
            <div class="modal-footer">
                <button id="editTemplateSubmit" class="btn btn-primary" data-ui-loader><?= Yii::t('CustomPagesModule.base', 'Save'); ?></button>
                <button type="button" class="btn btn-primary" data-dismiss="modal"><?php echo Yii::t('CustomPagesModule.base', 'Cancel'); ?></button>
            </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>

<script type="text/javascript">
    $('#editTemplateSubmit').on('click', function (evt) {
        evt.preventDefault();
        
        var $form = $(this).closest('form');
        
        var $disabled = $form.find(':disabled');

        $disabled.each(function() {
            var name = $(this).attr('name');
            $form.find('[name="'+name+'"]').remove();
        });
        
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