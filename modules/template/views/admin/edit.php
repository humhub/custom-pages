<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\libs\Html;
use humhub\modules\ui\form\widgets\ActiveForm;
use humhub\modules\ui\view\components\View;
use humhub\widgets\Button;
use humhub\modules\custom_pages\modules\template\models\Template;

/* @var Template $model */
/* @var View $this */
?>
<div class="panel panel-default">
    <?= $this->render('editHeader', [
        'model' => $model,
        'description' => Yii::t('CustomPagesModule.template', 'Define general settings for the template.'),
    ]) ?>

    <div class="panel-body">
        <?php $form = ActiveForm::begin() ?>

        <?= $form->field($model, 'name') ?>
        <?= $form->field($model, 'description')->textarea(['id' => 'template-form-description', 'rows' => 3]) ?>
        <?= $form->field($model, 'type')->dropDownList($model::getTypeOptions()) ?>

        <div id="template-allow-for-spaces"<?= $model->isLayout() ? '' : ' style="display:none"'?>>
            <?= $form->field($model, 'allow_for_spaces')->checkbox() ?>
        </div>

        <?= $model->canEdit() ? Button::save()->submit() : '' ?>

        <?php $form::end(); ?>
    </div>
</div>
<script <?= Html::nonce() ?>>
$('#template-type').change(function () {
    $('#template-allow-for-spaces').toggle(
        $(this).val() === '<?= Template::TYPE_LAYOUT?>' ||
        $(this).val() === '<?= Template::TYPE_SNIPPET_LAYOUT?>'
    );
});
</script>
