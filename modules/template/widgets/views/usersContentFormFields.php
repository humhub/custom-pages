<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\modules\custom_pages\modules\template\models\UsersContent;
use humhub\modules\ui\form\widgets\ActiveForm;
use humhub\modules\user\widgets\UserPickerField;

/* @var UsersContent $model */
/* @var ActiveForm $form */
?>
<div class="records-content-form-fields" data-type="static">
    <?= $form->field($model, 'options[static]')->widget(UserPickerField::class, [
        'minInput' => 2,
    ])->label(Yii::t('CustomPagesModule.template', 'Select users')) ?>
</div>

<div class="records-content-form-fields" data-type="group">
    <?= $form->field($model, 'options[group]')
        ->dropDownList($model->getGroupOptions())
        ->label(Yii::t('CustomPagesModule.template', 'Select group')) ?>
</div>

<div class="records-content-form-fields" data-type="friend">
    <?= $form->field($model, 'options[friend]')->widget(UserPickerField::class, [
        'minInput' => 2,
        'maxSelection' => 1,
    ])->label(Yii::t('CustomPagesModule.template', 'User')) ?>
</div>

<div class="records-content-form-fields" data-type="group,friend">
    <?= $form->field($model, 'options[limit]')
        ->label(Yii::t('CustomPagesModule.template', 'Limit')) ?>
</div>
