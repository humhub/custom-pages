<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\libs\Html;
use humhub\modules\custom_pages\modules\template\elements\BaseRecordsElement;
use humhub\modules\ui\form\widgets\ActiveForm;
use humhub\modules\ui\view\components\View;

/* @var BaseRecordsElement $model */
/* @var ActiveForm $form */
/* @var View $this */
?>
<?= $form->field($model, 'type')->dropDownList($model->getTypes(), ['class' => 'records-content-form-type']) ?>

<?= $this->render($model->subFormView, [
    'form' => $form,
    'model' => $model,
]) ?>

<script <?= Html::nonce() ?>>
$(document).on('change', '.records-content-form-type', function () {
    const type = $(this).val();
    $(this).closest('form').find('.records-content-form-fields').each(function () {
        $(this).toggle($(this).data('type').match(new RegExp('(^|,)' + type + '(,|$)')) !== null);
    });
});
$('.records-content-form-type').trigger('change');
</script>
