<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\modules\custom_pages\modules\template\elements\UsersElement;
use humhub\modules\ui\form\widgets\ActiveForm;
use humhub\modules\user\widgets\UserPickerField;

/* @var UsersElement $model */
/* @var ActiveForm $form */
?>
<div class="records-content-form-fields" data-type="static">
    <?= $form->field($model, 'static')->widget(UserPickerField::class, [
        'minInput' => 2,
    ]) ?>
</div>

<div class="records-content-form-fields" data-type="group">
    <?= $form->field($model, 'group')->dropDownList($model->getGroupOptions()) ?>
</div>

<div class="records-content-form-fields" data-type="friend">
    <?= $form->field($model, 'friend')->widget(UserPickerField::class, [
        'minInput' => 2,
        'maxSelection' => 1,
    ]) ?>
</div>

<div class="records-content-form-fields" data-type="group,friend">
    <?= $form->field($model, 'limit') ?>
</div>
