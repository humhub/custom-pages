<?php

use humhub\modules\custom_pages\models\CustomPage;
use humhub\modules\custom_pages\modules\template\services\TemplateInstanceRendererService;
use humhub\modules\custom_pages\widgets\SnippetContent;
use humhub\widgets\menu\MenuLink;

/* @var $model CustomPage */
/* @var $canEdit bool */

$navigation = !$canEdit ? [] : [
    new MenuLink(['label' => Yii::t('CustomPagesModule.base', 'Edit'), 'url' => $model->getEditUrl(), 'icon' => 'pencil']),
];
?>

<?= SnippetContent::widget([
    'model' => $model,
    'content' => TemplateInstanceRendererService::instance($model)->render(),
    'navigation' => $navigation,
]) ?>
