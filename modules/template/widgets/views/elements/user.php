<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\modules\custom_pages\modules\template\elements\UserElement;
use humhub\modules\ui\form\widgets\ActiveForm;
use humhub\modules\user\widgets\UserPickerField;

/* @var UserElement $model */
/* @var ActiveForm $form */
?>
<?= $form->field($model, 'guid')->widget(UserPickerField::class, [
    'minInput' => 2,
    'maxSelection' => 1,
]) ?>
