<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\libs\Html;
use humhub\modules\ui\form\widgets\ActiveForm;
use humhub\modules\custom_pages\widgets\AdminMenu;
use humhub\widgets\Button;
use yii\helpers\Url;
use humhub\modules\custom_pages\modules\template\models\Template;

/* @var Template $model */
?>
<div class="panel panel-default">
    <div class="panel-heading"><?= Yii::t('CustomPagesModule.base', '<strong>Custom</strong> Pages') ?></div>
    <?= AdminMenu::widget() ?>

    <div class="panel-body">
        <?= Button::defaultType(Yii::t('CustomPagesModule.base', 'Go Back'))
            ->icon('arrow-left')
            ->link(['index'])
            ->right() ?>

        <?php if ($model->isNewRecord): ?>
            <?= $model->id
                ? Yii::t('CustomPagesModule.template', '<strong>Copying</strong> {type}', ['type' => Template::getTypeTitle($model->type)])
                : Yii::t('CustomPagesModule.template', '<strong>Creating</strong> new Template') ?>
        <?php else: ?>
            <?php if ($model->canEdit()) : ?>
                <?= Yii::t('CustomPagesModule.template', '<strong>Editing:</strong> {templateName}', ['templateName' => Html::encode($model->name)]) ?>
            <?php else : ?>
                <?= Yii::t('CustomPagesModule.template', '<strong>Viewing:</strong> {templateName}', ['templateName' => Html::encode($model->name)]) ?>
            <?php endif; ?>
        <?php endif; ?>
        <br><br>

    <?php if (!$model->isNewRecord): ?>
        </div>
        <ul class="nav nav-tabs tab-sub-menu" id="tabs">
            <li class="active">
                <?= Html::a(Yii::t('CustomPagesModule.base', 'General'), Url::to(['edit', 'id' => $model->id])); ?>
            </li>
            <li>
                <?= Html::a(Yii::t('CustomPagesModule.base', 'Source'), Url::to(['edit-source', 'id' => $model->id])); ?>
            </li>
            <li>
                <?= Html::a(Yii::t('CustomPagesModule.base', 'Usage'), Url::to(['edit-usage', 'id' => $model->id])); ?>
            </li>
        </ul>
        <div class="panel-body">
    <?php endif; ?>

        <?php $form = ActiveForm::begin() ?>

        <?= $form->field($model, 'name') ?>
        <?= $form->field($model, 'description')->textarea(['id' => 'template-form-description', 'rows' => 3]) ?>
        <?= $form->field($model, 'type')->dropDownList($model::getTypeOptions()) ?>

        <div id="template-allow-for-spaces"<?= $model->isLayout() ? '' : ' style="display:none"'?>>
            <?= $form->field($model, 'allow_for_spaces')->checkbox() ?>
        </div>

        <?= $model->canEdit() ? Button::save()->submit() : '' ?>
        <?= $model->isNewRecord ? '' : Button::defaultType(Yii::t('CustomPagesModule.template', 'Copy'))
            ->icon('copy')
            ->link(Url::toRoute(['copy', 'id' => $model->id])) ?>

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
