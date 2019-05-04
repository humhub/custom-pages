<?php

use humhub\modules\custom_pages\modules\template\models\TextContent;

?>
<?= $form->field($model, 'content')->textInput(['maxlength' => 255])->label(false) ?>

<?php if(in_array($model->scenario, [TextContent::SCENARIO_EDIT_ADMIN, TextContent::SCENARIO_CREATE])) : ?>
    <?= $form->field($model, 'inline_text')->checkbox() ?>
    <div class="alert alert-info">
        <?= Yii::t('CustomPagesModule.base', 'Select this setting for visible text nodes only. Uncheck this setting in case this element is used for example as HTML attribute value.'); ?>
    </div>
<?php endif; ?>
