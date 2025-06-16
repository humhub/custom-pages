<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\modules\custom_pages\modules\template\elements\UserElement;
use humhub\modules\ui\form\widgets\ActiveForm;

/* @var UserElement $model */
/* @var ActiveForm $form */
?>
<?= $form->field($model, 'contentRecordId')->textInput(['maxlength' => 255])->label(true) ?>
