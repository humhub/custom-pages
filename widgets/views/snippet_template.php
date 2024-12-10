<?php

use humhub\modules\custom_pages\models\CustomPage;
use humhub\modules\custom_pages\modules\template\services\TemplateInstanceRendererService;
use humhub\modules\custom_pages\widgets\SnippetContent;

/* @var $model CustomPage */
/* @var $canEdit bool */

$navigation = !$canEdit ? [] : [
    '<a href="'.$model->getEditUrl().'"><i class="fa fa-pencil"></i>' . Yii::t('CustomPagesModule.base', 'Edit') . '</a>'
];
?>

<?= SnippetContent::widget([
    'model' => $model,
    'content' => TemplateInstanceRendererService::instance($model)->render(Yii::$app->request->get('editMode')),
    'navigation' => $navigation,
]) ?>
