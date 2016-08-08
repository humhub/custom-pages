<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;

/* @var $model humhub\modules\custom_pages\modules\template\models\forms\TemplateElementForm */

?>
<div class="modal-dialog <?= (empty($model->contentMap)) ? 'modal-dialog-normal' : 'modal-dialog-large'  ?>">
    <div class="modal-content media">
        <?php $form = ActiveForm::begin(['enableClientValidation' => false]);?>
            <?= Html::hiddenInput('editMultipleElements', true); ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">
                   <?= $title ?>
                </h4>
            </div>
            <div class="modal-body media-body"> 
                <?php $first = true ?>
                <?php foreach($model->contentMap as $key => $contentItem) :?>
                    <?php if(!$first) :?>
                        <hr />
                    <?php endif; ?>
                    <?php $first = false ?>
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
                <?php endforeach; ?>
                <?php if(empty($model->contentMap)) :?>
                    <div class="text-center">
                        <?= Yii::t('CustomPagesModule.widgets_views_editMultipleElements', 'This template does not contain any elements yet.') ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <?php if(!$first) :?>
                    <button id="editTemplateSubmit" class="btn btn-primary" data-ui-loader><?= Yii::t('CustomPagesModule.base', 'Save'); ?></button>
                    <button type="button" class="btn btn-primary" data-dismiss="modal"><?php echo Yii::t('CustomPagesModule.base', 'Cancel'); ?></button>
                <?php else: ?>
                    <button type="button" class="btn btn-primary" data-dismiss="modal"><?php echo Yii::t('CustomPagesModule.base', 'Back'); ?></button>
                <?php endif; ?>
                
            </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>

<script type="text/javascript">
    $('#editTemplateSubmit').on('click', function (evt) {
        evt.preventDefault();
        
        var $form = $(this).closest('form');
      
        var $disabled = $form.find(':disabled');

        // TODO: This is rather hacky, we do not want to save the definition fields in this cas
        // Should rather be handled in the backend!
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
                    $(document).trigger('templateMultipleElementEditSuccess', [json]);
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