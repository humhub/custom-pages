<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

use humhub\helpers\Html;
use humhub\widgets\bootstrap\Button;
use humhub\widgets\ContentCard;
use humhub\widgets\form\ActiveForm;

/* @var $this \humhub\components\View */
/* @var $model \humhub\modules\custom_pages\models\forms\SettingsForm */

?>

<?php ContentCard::begin([
    'title' => Yii::t('CustomPagesModule.view', 'Settings'),
]) ?>
    <?php $form = ActiveForm::begin() ?>
        <?= $form->field($model, 'phpPagesActive')->checkbox(['id' => 'phpPagesActive']); ?>
        <div id="phpPageSettings">
            <?= $form->field($model, 'phpGlobalPagePath'); ?>
            <?= $form->field($model, 'phpGlobalSnippetPath'); ?>
            <?= $form->field($model, 'phpContainerPagePath'); ?>
            <?= $form->field($model, 'phpContainerSnippetPath'); ?>
            <div class="alert alert-info">
                <?= Yii::t('CustomPagesModule.base', 'Always make a backup of your view files outside of your production environment!') ?>
            </div>
            <div class="alert alert-danger">
                <?= Yii::t('CustomPagesModule.base', 'Please keep in mind that php based pages are vulnerable to security issues, especially when handling user input. For more information, please have a look at <a href="{url}">Yii\'s Security best practices</a>.', ['url' => 'http://www.yiiframework.com/doc-2.0/guide-security-best-practices.html']) ?>
            </div>
        </div>

        <?= Button::save()->submit() ?>
    <?php ActiveForm::end() ?>
<?php ContentCard::end() ?>

<?= Html::script(<<<JS
    var checkPhpPagesActive = function() {
        if($('#phpPagesActive').is(':checked')) {
            $('#phpPageSettings').find('input').prop('disabled', false);
        } else {
            $('#phpPageSettings').find('input').prop('disabled', true);
        }
    };

    $('#phpPagesActive').on('change', function() {
        checkPhpPagesActive();
    });

    checkPhpPagesActive();
JS
) ?>
