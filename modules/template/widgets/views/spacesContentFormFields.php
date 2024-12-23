<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\modules\content\widgets\ContainerTagPicker;
use humhub\modules\custom_pages\modules\template\models\SpacesContent;
use humhub\modules\space\widgets\SpacePickerField;
use humhub\modules\ui\form\widgets\ActiveForm;
use humhub\modules\user\widgets\UserPickerField;
use yii\helpers\Url;

/* @var SpacesContent $model */
/* @var ActiveForm $form */
?>
<div class="records-content-form-fields" data-type="static">
    <?= $form->field($model, 'options[static]')->widget(SpacePickerField::class, [
        'minInput' => 2,
    ])->label(Yii::t('CustomPagesModule.template', 'Select spaces')) ?>
</div>

<div class="records-content-form-fields" data-type="member">
    <?= $form->field($model, 'options[member]')->widget(UserPickerField::class, [
        'minInput' => 2,
        'maxSelection' => 1,
    ])->label(Yii::t('CustomPagesModule.template', 'User'))
    ->hint(Yii::t('CustomPagesModule.template', 'When no user is selected, the current logged in user will be used.')) ?>

    <?= $form->field($model, 'options[memberType]')
        ->dropDownList($model->getMemberTypes())
        ->label(Yii::t('CustomPagesModule.template', 'Space member type')) ?>
</div>

<div class="records-content-form-fields" data-type="tag">
    <?= $form->field($model, 'options[tag]')->widget(ContainerTagPicker::class, [
        'url' => Url::to(['/space/browse/search-tags-json']),
        'minInput' => 2,
        'maxSelection' => 1,
    ])->label(Yii::t('CustomPagesModule.template', 'Tag')) ?>
</div>

<div class="records-content-form-fields" data-type="member,tag">
    <?= $form->field($model, 'options[limit]')
        ->label(Yii::t('CustomPagesModule.template', 'Limit')) ?>
</div>
