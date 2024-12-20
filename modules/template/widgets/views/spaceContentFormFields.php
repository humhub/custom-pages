<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\modules\custom_pages\modules\template\models\SpaceContent;
use humhub\modules\space\widgets\SpacePickerField;
use humhub\modules\ui\form\widgets\ActiveForm;

/* @var SpaceContent $model */
/* @var ActiveForm $form */
?>
<?= $form->field($model, 'guid')->widget(SpacePickerField::class, [
    'minInput' => 2,
    'maxSelection' => 1,
]) ?>
