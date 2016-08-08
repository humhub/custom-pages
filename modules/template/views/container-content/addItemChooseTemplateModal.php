<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;

/* @var $allowedTemplateSelection array */

?>
<div class="modal-dialog modal-dialog-normal">
    <div class="modal-content media">
        <?php $form = ActiveForm::begin(['action' => $action, 'enableClientValidation' => false]);?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">
                   <?= Yii::t('CustomPagesModule.modules_template_views_element_addItemChooseTemplate', 'Choose a template'); ?>
                </h4>
            </div>
            <div class="modal-body media-body">  
                <div class="form-group field-templateelement-name required">
                    <label class="control-label" for="templateSelection"><?= Yii::t('CustomPagesModule.modules_template_views_element_addItemChooseTemplate', 'Template'); ?></label>
                    <?= Html::dropDownList('templateId', null, $allowedTemplateSelection, ['id' => 'templateSelection', 'class' => 'form-control']) ?>
                </div>
            </div>
            <div class="modal-footer">
                <button id="chooseTemplateSubmit" class="btn btn-primary"><?= Yii::t('CustomPagesModule.base', 'Submit'); ?></button>
                <button type="button" class="btn btn-primary" data-dismiss="modal"><?php echo Yii::t('CustomPagesModule.base', 'Cancel'); ?></button>
            </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>

<script type="text/javascript">
    $('#chooseTemplateSubmit').on('click', function (evt) {
        evt.preventDefault();
        
        var $form = $(this).closest('form');
        var action = $form.attr('action');

        $.ajax(action, {
            type: 'POST',
            dataType: 'json',
            data: $form.serialize(),
            beforeSend: function () {
                // TODO: implement global reset for modal...
                $('#globalModal').html('<div class="modal-dialog"><div class="modal-content"><div class="modal-body"> <div class="loader"><div class="sk-spinner sk-spinner-three-bounce"><div class="sk-bounce1"></div><div class="sk-bounce2"></div><div class="sk-bounce3"></div></div></div></div></div></div>');
                setModalLoader();
            },
            success: function (json) {
                $('#globalModal').html(json.content);
            }
        });
    });
</script>