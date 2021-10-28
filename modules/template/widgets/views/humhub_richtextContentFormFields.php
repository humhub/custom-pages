<?php
/* @var $model humhub\modules\custom_pages\modules\template\models\RichtextContent */
/* @var $form \humhub\modules\ui\form\widgets\ActiveForm */

use yii\helpers\Html;
use humhub\modules\content\widgets\richtext\RichTextField;

?>

<?= $form->field($model, 'content')->widget(RichTextField::class) ?>

<?php foreach ($model->fileList as $file) : ?>
    <?= Html::hiddenInput($model->formName().'[fileList][]', $file); ?>
<?php endforeach; ?>
