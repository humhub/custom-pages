<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\modules\custom_pages\modules\template\elements\BaseContentRecordsElement;
use humhub\modules\space\widgets\SpacePickerField;
use humhub\modules\topic\widgets\TopicPicker;
use humhub\modules\ui\form\widgets\ActiveForm;
use humhub\modules\user\widgets\UserPickerField;

/* @var BaseContentRecordsElement $model */
/* @var ActiveForm $form */
?>
<div class="records-content-form-fields" data-type="options">
    <?= $form->field($model, 'space')->widget(SpacePickerField::class) ?>
    <?= $form->field($model, 'author')->widget(UserPickerField::class) ?>
    <?= $form->field($model, 'topic')->widget(TopicPicker::class) ?>
    <?= $form->field($model, 'filter')->checkboxList($model->getContentFilterOptions()) ?>
    <?= $form->field($model, 'limit') ?>
</div>

<?= $this->render($model->contentFormView, [
    'form' => $form,
    'model' => $model,
]) ?>
