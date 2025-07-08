<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\helpers\Html;
use humhub\modules\custom_pages\modules\template\widgets\TemplateMenu;
use humhub\modules\custom_pages\modules\template\widgets\TemplateTabs;
use humhub\modules\custom_pages\widgets\AdminMenu;
use humhub\modules\custom_pages\modules\template\models\Template;
use humhub\widgets\bootstrap\Button;

/* @var Template $model */
/* @var string $description */
?>
<div class="panel-heading"><?= Yii::t('CustomPagesModule.base', '<strong>Custom</strong> Pages') ?></div>
<?= AdminMenu::widget() ?>

<div class="panel-body">
    <?= TemplateMenu::widget(['model' => $model]) ?>

    <?= Button::light(Yii::t('CustomPagesModule.base', 'Go Back'))
        ->icon('arrow-left')
        ->link(['index'])
        ->right() ?>

    <?php if ($model->isNewRecord): ?>
        <?= $model->id
            ? Yii::t('CustomPagesModule.template', '<strong>Copying</strong> {type}', ['type' => Template::getTypeTitle($model->type)])
            : Yii::t('CustomPagesModule.template', '<strong>Creating</strong> new Template') ?>
    <?php else: ?>
        <?php if ($model->canEdit()) : ?>
            <?= Yii::t('CustomPagesModule.template', '<strong>Editing:</strong> {templateName}', ['templateName' => Html::encode($model->name)]) ?>
        <?php else : ?>
            <?= Yii::t('CustomPagesModule.template', '<strong>Viewing:</strong> {templateName}', ['templateName' => Html::encode($model->name)]) ?>
        <?php endif; ?>
    <?php endif; ?>

    <?php if (isset($description)) : ?>
    <div class="help-block">
        <?= $description ?>
    </div>
    <?php endif; ?>

    <?php if ($model->is_default && !$model->isNewRecord): ?>
    <div class="alert alert-warning">
        <?= Yii::t('CustomPagesModule.template', 'This is a default system template and cannot be modified. To make changes, please create a copy.') ?>
    </div>
    <?php endif; ?>
</div>

<?= TemplateTabs::widget(['model' => $model]) ?>
