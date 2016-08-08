<?php 
    /* @var $model humhub\modules\custom_pages\modules\template\models\template\ImageContent */
    /* @var $form humhub\compat\CActiveForm */

use yii\helpers\Url;
use yii\helpers\Html;

$sguid = Yii::$app->request->get('sguid');

$uploadUrl = Url::to(['/file/file/upload']);

$disableDefinition = !$isAdminEdit && $model->definition->is_default;

?>

<hr class="hr-text" data-content="<?= Yii::t('CustomPagesModule.base', 'Definition'); ?>" />

<?= $form->field($model->definition, 'height')->textInput(['disabled' => $disableDefinition]); ?>
<?= $form->field($model->definition, 'width')->textInput(['disabled' => $disableDefinition]); ?>
<?= $form->field($model->definition, 'style')->textInput(['disabled' => $disableDefinition]); ?>    

<?= \humhub\modules\custom_pages\modules\template\widgets\EditContentSeperator::widget(['isAdminEdit' => $isAdminEdit]) ?>

<?= $form->field($model, 'file_guid')->hiddenInput(['class' => 'file-guid']); ?>

<?= Html::fileInput('files')?>
<br />
<button class="uploadNewImage" class="btn btn-primary" data-form-name="<?= $model->formName()?>"><?= Yii::t('CustomPagesModule.base', 'Upload'); ?></button>
<br />
<?php if($model->hasFile()) : ?>
    <img class="preview" src="<?= $model->getUrl() ?>"/>
<?php else:?>
    <img class="preview" style="display:none;" src="#"/>
<?php endif;?>
<br />
<?= $form->field($model, 'alt')->textInput(); ?>
    
<script>
    $('.uploadNewImage').off('click').on('click', function(evt) {
        evt.preventDefault();
        
        var $form = $(this).closest('form');
        var $this = $(this);
        var formData = new FormData($form[0]);
        
        $.ajax({
            url: '<?= $uploadUrl ?>',  //Server script to process data
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
            success: function(json) {
                var files = json.files;
                var $preview = $this.siblings('.preview');
                $preview.attr('src', files[0].url);
                $preview.fadeIn('fast');
                $form.find('.file-guid').val(files[0].guid);
                $form.append('<input type="hidden" name="'+$this.data('form-name')+'[fileList][]" value="' + files[0].guid + '" />');
            },
            //error: errorHandler,
            // Form data
            data: formData,
            //Options to tell jQuery not to process data or worry about content-type.
            cache: false,
            contentType: false,
            processData: false
        });
    });
    
    var ckeditorAddUploadedFile = function (guid) {
        var form = $(CKEDITOR.currentInstance.container.$).closest('form');
        var modelFormName = $(CKEDITOR.currentInstance.element.$).data('form-name');
        $(form).append('<input type="hidden" name="'+modelFormName+'[fileList][]" value="' + guid + '" />');
    };
</script>