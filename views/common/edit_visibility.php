<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\libs\Html;
use humhub\modules\admin\models\forms\UserEditForm;
use humhub\modules\custom_pages\models\CustomPage;
use humhub\modules\ui\form\widgets\ActiveForm;
use humhub\modules\ui\form\widgets\MultiSelect;

/* @var $page CustomPage */
/* @var $form ActiveForm */
?>
<?= $form->field($page, 'visibility')->radioList($page->visibilityService->getOptions())->label(false) ?>

<div data-cp-visibility-options="<?= CustomPage::VISIBILITY_CUSTOM ?>"<?= $page->visibilityService->isCustom() ? '' : ' style="display:none"' ?>>
    <?= $form->field($page, 'visibility_groups')->widget(MultiSelect::class, [
        'items' => UserEditForm::getGroupItems(),
        'options' => ['data-tags' => 'false'],
    ]) ?>
    <?= $form->field($page, 'visibility_languages')->widget(MultiSelect::class, [
        'items' => Yii::$app->i18n->getAllowedLanguages(),
        'options' => ['data-tags' => 'false'],
    ]) ?>
</div>

<script <?= Html::nonce() ?>>
$(document).one('humhub:ready', function () {
        $('input[type="radio"][name="CustomPage[visibility]"]').click(function () {
            $('.infoAdminOnly').toggle($(this).val() == <?= CustomPage::VISIBILITY_ADMIN ?>);
            $('[data-cp-visibility-options]').hide();
            $('[data-cp-visibility-options=' + $(this).val() + ']').show();
        });
    }
);
</script>
