<?php

use humhub\modules\custom_pages\widgets\SnippetContent;
use humhub\modules\custom_pages\controllers\ViewController;

/* @var $model \humhub\modules\custom_pages\models\CustomContentContainer */
/* @var $canEdit bool */

$controller = new ViewController(null, null);
$content = $controller->renderTemplate($model);
$canEdit = $controller->isCanEdit();

$navigation = (!$canEdit) ? [] : [
    '<a href="'.$model->getEditUrl().'"><i class="fa fa-pencil"></i>' . Yii::t('CustomPagesModule.base', 'Edit') . '</a>'
];
?>

<?=
SnippetContent::widget([
    'model' => $model,
    'content' => $content,
    'navigation' => $navigation
]);
?>