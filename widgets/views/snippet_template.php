<?php

use yii\helpers\Url;

$controller = new \humhub\modules\custom_pages\controllers\ViewController(null, null);
$content = $controller->renderTemplate($model);
$canEdit = $controller->isCanEdit();

if($contentContainer != null) {
    $editUrl = $contentContainer->createUrl('/custom_pages/container-snippet/edit-snippet', ['id' => $model->id]);
} else {
    $editUrl = Url::to(['/custom_pages/snippet/edit-snippet', 'id' => $model->id]);
}

$navigation = (!$canEdit) ? [] : [
    '<a href="'.$editUrl.'" class="panel-collapse"><i class="fa fa-pencil"></i>' . Yii::t('CustomPagesModule.base', 'Edit') . '</a>'
];
?>

<?=
\humhub\modules\custom_pages\widgets\SnippetContent::widget([
    'model' => $model,
    'content' => $content,
    'navigation' => $navigation
]);
?>