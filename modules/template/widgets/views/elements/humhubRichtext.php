<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\modules\content\widgets\richtext\RichTextField;
use humhub\modules\custom_pages\modules\template\elements\RichtextElement;
use humhub\modules\ui\form\widgets\ActiveForm;
use yii\helpers\Html;

/* @var $model RichtextElement */
/* @var $form ActiveForm */
?>
<?= $form->field($model, 'content')->widget(RichTextField::class) ?>

<?php foreach ($model->fileList as $file) : ?>
    <?= Html::hiddenInput($model->formName() . '[fileList][]', $file) ?>
<?php endforeach; ?>
