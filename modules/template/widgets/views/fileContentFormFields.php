<?php
/* @var $model humhub\modules\custom_pages\modules\template\models\template\ImageContent */
/* @var $form humhub\compat\CActiveForm */

use yii\helpers\Url;
use yii\helpers\Html;

$uploadUrl = Url::to(['/file/file/upload']);
?>

<?= $form->field($model, 'file_guid')->hiddenInput(['class' => 'file-guid'])->label(false); ?>

<div class="row">
    <div class="col-md-3 uploadContainer">
        <label class="btn btn-default btn-file btn-primary btn-sm">
            <?= Yii::t('CustomPagesModule.modules_template_widgets_views_fileContentFormFields', 'Choose New File') ?> 
            <?= Html::fileInput('files', null, ['class' => 'uploadElementFile', 'style' => 'display:none;']) ?>
        </label>
        <br />
        <button style="display:none;" class="uploadFile btn btn-primary btn-sm" data-form-name="<?= $model->formName() ?>"><?= Yii::t('CustomPagesModule.base', 'Upload'); ?></button>
    </div>
    <div class="col-md-9 previewContainer">
        <p class="file-text">
            <?php if ($model->hasFile() && $model->getFile() != null) : ?>
                <a target="_blank" href="<?= $model->getUrl() ?>"><?= $model->getFile()->file_name ?></a>
            <?php else: ?>
                <?= Yii::t('CustomPagesModule.base', '<strong>No file available.</strong>'); ?>
            <?php endif; ?>
                
        </p>
        <br />
    </div>
</div>

<script>
    $('.uploadElementFile').off('change').on('change', function () {
        if (!this.files.length) {
            return;
        }
        
        $this = $(this);
        $('.imageLoader').remove();
        var $loader = $('<div class="imageLoader loader"><div class="sk-spinner sk-spinner-three-bounce"><div class="sk-bounce1"></div><div class="sk-bounce2"></div><div class="sk-bounce3"></div></div></div>');
        $loader.hide();

        var $preview = $this.closest('.uploadContainer').next().find('.file-text');
        var isEmpty = false;
        if (!$preview.is(':visible')) {
            $preview = $preview.next();
            isEmpty = true;
        }

        var offset = $preview.offset();
        var height = $preview.outerHeight();
        var width = $preview.outerWidth();

        $loader.css({
            left: offset.left,
            top: offset.top,
            height: height,
            width: width,
            'line-height': $preview.height() + 'px',
        });

        if (isEmpty) {
            $loader.css('background-color', 'transparent');
        }

        $('body').append($loader);

        $loader.show();

        $(this).parent().siblings('.uploadFile').trigger('click');
    });

    var lastScrollTop = 0;
    $('#globalModal').off('scroll').on('scroll', function (event) {
        var st = $(this).scrollTop();
        if (st > lastScrollTop) {
            var value = Math.abs(st - lastScrollTop);
            $('.imageLoader').animate({
                top: '-=' + value
            }, 0);
        } else {
            var value = Math.abs(st - lastScrollTop);
            $('.imageLoader').animate({
                top: '+=' + value
            }, 0);
        }
        lastScrollTop = st;
    });

    $('.uploadFile').off('click').on('click', function (evt) {
        evt.preventDefault();

        var $this = $(this);
        var $form = $this.closest('form');
        var formData = new FormData($form[0]);

        $.ajax({
            url: '<?= $uploadUrl ?>', //Server script to process data
            type: 'POST',
            /*xhr: function() {  // Custom XMLHttpRequest
             var myXhr = $.ajaxSettings.xhr();
             if(myXhr.upload){ // Check if upload property exists
             myXhr.upload.addEventListener('progress',progressHandlingFunction, false); // For handling the progress of the upload
             }
             return myXhr;
             },*/
            //Ajax events
            //beforeSend: beforeSendHandler,
            success: function (json) {
                // remove the disabled class of loader but disable the upload button
                $this.html('<?= Yii::t('CustomPagesModule.base', 'Upload'); ?>').removeClass('disabled');
                $this.prop('disabled', true);
                var files = json.files;
                var $preview = $this.parent().next().find('.file-text');
                $preview.html('<a href='+files[0].url+' target="_blank">'+files[0].title+'</a>');
                $preview.fadeIn('fast');
                $form.find('.file-guid').val(files[0].guid);
                $form.append('<input type="hidden" name="' + $this.data('form-name') + '[fileList][]" value="' + files[0].guid + '" />');
                $('.imageLoader').remove();
            },
            data: formData,
            //Options to tell jQuery not to process data or worry about content-type.
            cache: false,
            contentType: false,
            processData: false
        });
    });
</script>