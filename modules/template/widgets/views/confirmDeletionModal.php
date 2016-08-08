<?php

use yii\helpers\Url;

/* @var $title string */
/* @var $message string */


?>
<div class="modal-dialog modal-dialog-extra-small animated pulse">
    <div class="modal-content media">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">
                   <?= $title?>
                </h4>
            </div>
            <div class="modal-body text-center">  
                <p><?= $message ?></p>
            </div>
            <div class="modal-footer">
                <button id="editTemplateSubmit" data-ui-loader class="btn btn-primary"><?= Yii::t('CustomPagesModule.base', 'Delete'); ?></button>
                <button type="button" class="btn btn-primary" data-dismiss="modal"><?php echo Yii::t('CustomPagesModule.base', 'Cancel'); ?></button>
            </div>
    </div>
</div>

<script type="text/javascript">
    $('#editTemplateSubmit').on('click', function (evt) {
        evt.preventDefault();
        

        $('textarea.ckeditorInput').each(function () {
            var $textarea = $(this);
            $textarea.val(CKEDITOR.instances[$textarea.attr('id')].getData());
         });

        $.ajax('<?= Url::to(); ?>', {
            type: 'POST',
            dataType: 'json',
            data: {confirmation: true},
            success: function (json) {
                if(json.success) {
                    $(document).trigger('<?= $successEvent ?>', [json]);
                } else {
                    $('#globalModal').html(json.content);
                }
            },
            error: function () {
                $(document).trigger('<?= $errorEvent ?>');
            }
        });
    });
</script>