<?php
/* @var $model humhub\modules\custom_pages\modules\template\models\TextContent */
/* @var $form humhub\compat\CActiveForm */
?>

<?= $form->field($model, 'content')->textInput(['maxlength' => 255])->label(false) ?>
