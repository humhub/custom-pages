<?php
/* @var $model humhub\modules\custom_pages\modules\template\models\RichtextContent */
/* @var $form humhub\compat\CActiveForm */
?>

<?= \humhub\modules\custom_pages\modules\template\widgets\EditContentSeperator::widget(['isAdminEdit' => $isAdminEdit]) ?>

<?= $form->field($model, 'content')->textInput(['maxlength' => 255])->label(false) ?>
