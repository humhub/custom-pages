<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\modules\custom_pages\modules\template\models\Template;
use humhub\modules\ui\form\widgets\ActiveForm;
use humhub\modules\ui\form\widgets\CodeMirrorInputWidget;
use humhub\widgets\Button;

/* @var $model Template */
?>
<div class="panel panel-default">
    <?= $this->render('editHeader', [
        'model' => $model,
        'description' => Yii::t('CustomPagesModule.template', 'Define template resources.'),
    ]) ?>

    <div class="panel-body">
        <?php $form = ActiveForm::begin() ?>

        <?= $form->field($model, 'css')->widget(CodeMirrorInputWidget::class, ['mode' => 'text/css']) ?>
        <?= $form->field($model, 'js')->widget(CodeMirrorInputWidget::class, ['mode' => 'text/javascript']) ?>

        <?php if ($model->canEdit()) : ?>
            <?= Button::save()->submit() ?>
        <?php endif; ?>

        <?php ActiveForm::end() ?>
    </div>
</div>
