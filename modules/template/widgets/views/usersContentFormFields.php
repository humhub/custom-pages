<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\modules\custom_pages\modules\template\models\UsersContent;
use humhub\modules\ui\form\widgets\ActiveForm;
use humhub\modules\ui\form\widgets\MultiSelect;
use humhub\modules\user\widgets\UserPickerField;

/* @var UsersContent $model */
/* @var ActiveForm $form */
?>
<div class="records-content-form-fields" data-type="static">
    <?= $form->field($model, 'optionUsers')->widget(UserPickerField::class, [
        'minInput' => 2,
    ]) ?>
</div>

<div class="records-content-form-fields" data-type="group">
    <?= $form->field($model, 'optionGroups')->widget(MultiSelect::class, [
        'items' => $model->getGroupOptions(),
    ]) ?>
</div>

<div class="records-content-form-fields" data-type="friend">
    <?= $form->field($model, 'optionFriend')->widget(UserPickerField::class, [
        'minInput' => 2,
        'maxSelection' => 1,
    ]) ?>
</div>
