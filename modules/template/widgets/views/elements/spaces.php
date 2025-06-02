<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\modules\content\widgets\ContainerTagPicker;
use humhub\modules\custom_pages\modules\template\elements\SpacesElement;
use humhub\modules\space\widgets\SpacePickerField;
use humhub\modules\ui\form\widgets\ActiveForm;
use humhub\modules\user\widgets\UserPickerField;
use yii\helpers\Url;

/* @var SpacesElement $model */
/* @var ActiveForm $form */
?>
<div class="records-content-form-fields" data-type="static">
    <?= $form->field($model, 'static')->widget(SpacePickerField::class, [
        'minInput' => 2,
    ]) ?>
</div>

<div class="records-content-form-fields" data-type="member">
    <?= $form->field($model, 'member')->widget(UserPickerField::class, [
        'minInput' => 2,
        'maxSelection' => 1,
    ]) ?>

    <?= $form->field($model, 'memberType')->dropDownList($model->getMemberTypes()) ?>
</div>

<div class="records-content-form-fields" data-type="tag">
    <?= $form->field($model, 'tag')->widget(ContainerTagPicker::class, [
        'url' => Url::to(['/space/browse/search-tags-json']),
        'minInput' => 2,
        'maxSelection' => 1,
    ]) ?>
</div>

<div class="records-content-form-fields" data-type="member,tag">
    <?= $form->field($model, 'limit') ?>
</div>
