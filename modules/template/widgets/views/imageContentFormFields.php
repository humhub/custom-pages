<?php
/* @var $model humhub\modules\custom_pages\modules\template\models\template\ImageContent */
/* @var $form humhub\compat\CActiveForm */

use yii\helpers\Url;
use yii\helpers\Html;
use humhub\modules\custom_pages\modules\template\widgets\CollapsableFormGroup;

$uploadUrl = Url::to(['/file/file/upload']);

$disableDefinition = !$isAdminEdit && $model->definition->is_default;
?>

<?= $form->field($model, 'file_guid')->hiddenInput(['class' => 'file-guid'])->label(false); ?>

<div class="row">
    <div class="col-md-3 uploadContainer">
        <label class="file-chooser btn btn-default btn-file btn-primary btn-sm" tabindex="0">
            <?= Yii::t('CustomPagesModule.modules_template_widgets_views_imageContentFormFields', 'Choose New Image') ?> 
            <?= Html::fileInput('files', null, ['class' => 'uploadElementImage', 'style' => 'display:none;']) ?>
        </label>

        <br />
        <br />
        <button style="display:none;" class="uploadNewImage btn btn-primary btn-sm" data-form-name="<?= $model->formName() ?>"><?= Yii::t('CustomPagesModule.base', 'Upload'); ?></button>
    </div>
    <div class="col-md-9 previewContainer">
        <?php if ($model->hasFile() && $model->getFile() != null) : ?>
            <img class="preview" src="<?= $model->getUrl() ?>"/>
        <?php else: ?>
            <img class="preview" style="display:none;" src="#"/>
            <p class="empty-image-text">
                <?= Yii::t('CustomPagesModule.base', '<strong>No image available.</strong>'); ?>
            </p>
        <?php endif; ?>
    </div>
</div>
<br />

<?php CollapsableFormGroup::begin(['defaultState' => false]) ?>

<div class="row">
    <div class="col-md-6">
        <?= $form->field($model->definition, 'height')->textInput(['disabled' => $disableDefinition]); ?>
    </div>
    <div class="col-md-6">
        <?= $form->field($model->definition, 'width')->textInput(['disabled' => $disableDefinition]); ?>
    </div>
</div>
<?= $form->field($model->definition, 'style')->textInput(['disabled' => $disableDefinition]); ?>    
<?= $form->field($model, 'alt')->textInput(); ?>
<?php CollapsableFormGroup::end() ?>

<script>
    $('.file-chooser').off('keyup').on('keyup', function (e) {
        if (e.which == 13) {
            e.preventDefault();
            $(this).find('.uploadElementImage').click();
        }
    });

    $('.uploadElementImage').off('change').on('change', function () {
        if (!this.files.length) {
            return;
        }

        $this = $(this);
        $('.imageLoader').remove();
        var $loader = $('<div class="imageLoader loader"><div class="sk-spinner sk-spinner-three-bounce"><div class="sk-bounce1"></div><div class="sk-bounce2"></div><div class="sk-bounce3"></div></div></div>');
        $loader.hide();

        var $preview = $this.closest('.uploadContainer').next().find('.preview');
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
            $preview.remove();
            $loader.css('background-color', 'transparent');
        }

        $('body').append($loader);

        $loader.show();

        $(this).parent().siblings('.uploadNewImage').trigger('click');
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

    $('.uploadNewImage').off('click').on('click', function (evt) {
        evt.preventDefault();

        var $this = $(this);
        var $form = $this.closest('form');
        var formData = new FormData();

        $.each($(this).parent().find('.uploadElementImage')[0].files, function (key, value) {
            formData.append('files', value);
        });

        $.ajax({
            url: '<?= $uploadUrl ?>', //Server script to process data
            type: 'POST',
            data: formData,
            //Options to tell jQuery not to process data or worry about content-type.
            cache: false,
            contentType: false,
            processData: false,
            success: function (json) {
                // remove the disabled class of loader but disable the upload button
                $this.html('<?= Yii::t('CustomPagesModule.base', 'Upload'); ?>').removeClass('disabled');
                $this.prop('disabled', true);
                var files = json.files;
                var $preview = $this.parent().next().find('.preview');
                $preview.attr('src', files[0].url);
                $preview.fadeIn('fast');
                $form.find('.file-guid').val(files[0].guid);
                $form.append('<input type="hidden" name="' + $this.data('form-name') + '[fileList][]" value="' + files[0].guid + '" />');
                $('.imageLoader').remove();
            }
        });
    });
</script>