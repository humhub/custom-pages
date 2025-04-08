<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\modules\custom_pages\modules\template\elements\ContainerElement;
use humhub\modules\custom_pages\modules\template\models\Template;
use humhub\modules\ui\form\widgets\ActiveForm;
use humhub\modules\ui\form\widgets\MultiSelect;

/* @var ContainerElement $model */
/* @var ActiveForm $form */
/* @var bool $isAdminEdit */

$disableDefinition = !$isAdminEdit && !$model->definition->isNewRecord;
?>
<?= $form->field($model->definition, 'templates')->widget(MultiSelect::class, [
    'items' => $model->definition->getAllowedTemplateOptions(),
    'disabled' => $disableDefinition,
]) ?>

<?= $form->field($model->definition, 'allow_multiple')->checkbox(['disabled' => $disableDefinition]) ?>

<?= $form->field($model->definition, 'is_inline')->checkbox(['disabled' => $disableDefinition]) ?>
