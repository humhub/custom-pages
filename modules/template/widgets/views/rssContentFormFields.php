<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\modules\custom_pages\modules\template\models\RssContent;
use humhub\modules\ui\form\widgets\ActiveForm;

/* @var RssContent $model */
/* @var ActiveForm $form */
?>
<?= $form->field($model, 'url')->textInput(['maxlength' => 1000]) ?>
<?= $form->field($model, 'cache_time') ?>
<?= $form->field($model, 'limit') ?>
