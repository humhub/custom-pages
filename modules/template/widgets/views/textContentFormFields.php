<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\modules\custom_pages\modules\template\elements\TextElement;
use humhub\modules\ui\form\widgets\ActiveForm;

/* @var ActiveForm $form */
/* @var TextElement $model */
?>
<?= $form->field($model, 'content')->textInput(['maxlength' => 255])->label(false) ?>

<?php if (in_array($model->scenario, [$model::SCENARIO_EDIT_ADMIN, $model::SCENARIO_CREATE])) : ?>
    <?= $form->field($model, 'inline_text')->checkbox() ?>
    <div class="alert alert-info">
        <?= Yii::t('CustomPagesModule.base', 'Select this setting for visible text nodes only. Uncheck this setting in case this element is used for example as HTML attribute value.') ?>
    </div>
<?php endif; ?>
