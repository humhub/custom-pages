<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\helpers\Html;
use humhub\modules\custom_pages\modules\template\models\Template;
use humhub\modules\custom_pages\modules\template\widgets\TemplateCard;
use humhub\modules\ui\form\widgets\CodeMirrorInputWidget;
use humhub\widgets\bootstrap\Button;
use humhub\widgets\form\ActiveForm;

/* @var $model Template */
?>
<?php TemplateCard::begin([
    'model' => $model,
    'description' => Html::encode(Yii::t('CustomPagesModule.template', 'Define template resources.')),
]) ?>
    <?php $form = ActiveForm::begin() ?>

    <?= $form->field($model, 'css')->widget(CodeMirrorInputWidget::class, ['mode' => 'text/css']) ?>
    <?= $form->field($model, 'js')->widget(CodeMirrorInputWidget::class, ['mode' => 'text/javascript']) ?>

    <?php if ($model->canEdit()) : ?>
        <?= Button::save()->submit() ?>
    <?php endif; ?>

    <?php ActiveForm::end() ?>
<?php TemplateCard::end() ?>
