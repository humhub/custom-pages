<?php 
    /* @var $model humhub\modules\custom_pages\models\forms\TemplateBlockForm */
    /* @var $form humhub\compat\CActiveForm */
?>
<?= $form->field($model, 'content')->textarea(['id' => 'htmlTextInput']); ?>
<script>
    // Replace the <textarea id="editor1"> with a CKEditor
    // instance, using default configuration.
    CKEDITOR.replace('htmlTextInput', {
        //filebrowserUploadUrl: '/uploader/upload.php'
    });
</script>
