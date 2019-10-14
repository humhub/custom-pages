<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

use yii\bootstrap\ActiveForm;
use humhub\libs\Html;

/* @var $this \humhub\components\View */
/* @var $subNav string */
/* @var $model \humhub\modules\custom_pages\models\forms\SettingsForm */

?>

<div class="panel panel-default">
    <div class="panel-heading"><?= Yii::t('CustomPagesModule.base', '<strong>Custom</strong> Pages'); ?></div>

    <?= $subNav ?>

    <div class="panel-body">
        <div class="clearfix">
            <h4><?= Yii::t('CustomPagesModule.views_admin_add', 'Settings') ?></h4>
            <div class="help-block">
                <?= Yii::t('CustomPagesModule.base', 'On this page you can configure general settings of your custom pages.') ?>
            </div>
        </div>

        <hr>

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

        <hr>

        <?php // Button::save()->submit() ?>
        <button class="btn btn-primary" data-ui-loader><?= Yii::t('base', 'Save') ?></button>

        <?php ActiveForm::end() ?>
    </div>



</div>

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
