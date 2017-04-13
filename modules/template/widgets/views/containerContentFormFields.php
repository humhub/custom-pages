<?php
/* @var $model humhub\modules\custom_pages\modules\template\models\template\ContainerContent */
/* @var $form humhub\compat\CActiveForm */

use yii\helpers\Url;
use yii\helpers\Html;
use humhub\modules\custom_pages\modules\template\models\Template;

$csrfTokenName = Yii::$app->request->csrfParam;
$csrfToken = Yii::$app->request->csrfToken;

$uploadUrl = Url::to(['/custom_pages/template/upload/upload-ckeditor-file', $csrfTokenName => $csrfToken]);

$model->definition->initAllowedTemplateSelection();

$disableDefinition = !$isAdminEdit && !$model->definition->isNewRecord;
?>

<div class="form-group field-templateelement-name">
    <label class="control-label" for="templateelement-name"><?= $model->getAttributeLabel('allowedTemplates') ?></label>
    <?= Html::dropDownList($model->formName() . '[definitionPostData][allowedTemplateSelection][]', $model->definition->allowedTemplateSelection, Template::getSelection(['type' => Template::TYPE_CONTAINER]), 
            ['class' => 'form-control multiselect_dropdown', 'disabled' => $disableDefinition, 'style' => 'style="width: 100%"', 'multiple' => '', 'size' => 4]); ?>
</div>
<p class="help-block">
    <?= Yii::t('CustomPagesModule.base', 'An empty allowed template selection will allow all container templates for this container.'); ?>
</p>
<br />


<?= $form->field($model->definition, 'allow_multiple')->checkbox(['disabled' => $disableDefinition]); ?>

<?= $form->field($model->definition, 'is_inline')->checkbox(['disabled' => $disableDefinition]); ?>