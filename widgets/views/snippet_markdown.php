<?php
use yii\helpers\Url;

if ($contentContainer != null) {
    $editUrl = $contentContainer->createUrl('/custom_pages/container-snippet/edit', ['id' => $model->id]);
} else {
    $editUrl = Url::to(['/custom_pages/snippet/edit', 'id' => $model->id]);
}

$navigation = (!$canEdit) ? [] : [
    '<a href="'.$editUrl.'" class="panel-collapse"><i class="fa fa-pencil"></i>' . Yii::t('CustomPagesModule.base', 'Edit') . '</a>'
];
?>
<?= \humhub\modules\custom_pages\widgets\SnippetContent::widget([
    'model' => $model,
    'content' => humhub\widgets\MarkdownView::widget(['markdown' => $model->getPageContent()]),
    'navigation' => $navigation
]); ?>