<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\components\View;
use humhub\helpers\Html;
use humhub\modules\custom_pages\modules\template\models\Template;
use humhub\modules\custom_pages\modules\template\widgets\TemplateCard;
use humhub\widgets\bootstrap\Button;
use humhub\widgets\form\ActiveForm;

/* @var Template $model */
/* @var View $this */
?>
<?php TemplateCard::begin([
    'model' => $model,
    'description' => Html::encode(Yii::t('CustomPagesModule.template', 'Define general settings for the template.')),
]) ?>
    <?php $form = ActiveForm::begin() ?>

    <?= $form->field($model, 'name') ?>
    <?= $form->field($model, 'description')->textarea(['id' => 'template-form-description', 'rows' => 3]) ?>
    <?= $form->field($model, 'type')->dropDownList($model::getTypeOptions()) ?>

    <div id="template-allow-for-spaces"<?= $model->isLayout() ? '' : ' class="d-none"' ?>">
        <?= $form->field($model, 'allow_for_spaces')->checkbox() ?>
    </div>

    <?= $form->field($model, 'template_instance_title_element')->dropDownList(
        $model->getTemplateInstanceTitleElementOptions(),
        ['prompt' => Yii::t('CustomPagesModule.template', 'Automatic (first suitable element)')],
    ) ?>

    <?= $model->canEdit() ? Button::save()->submit() : '' ?>

    <?php $form::end(); ?>
<?php TemplateCard::end() ?>
<script <?= Html::nonce() ?>>
$('#template-type').change(function () {
    $('#template-allow-for-spaces').toggle(
        $(this).val() === '<?= Template::TYPE_LAYOUT?>' ||
        $(this).val() === '<?= Template::TYPE_SNIPPET_LAYOUT?>'
    );
});
</script>
