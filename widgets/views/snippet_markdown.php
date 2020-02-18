<?php
use humhub\modules\custom_pages\widgets\SnippetContent;

/* @var $model \humhub\modules\custom_pages\models\CustomContentContainer */
/* @var $canEdit bool */

$navigation = (!$canEdit) ? [] : [
    '<a href="'.$model->getEditUrl().'"><i class="fa fa-pencil"></i>' . Yii::t('CustomPagesModule.base', 'Edit') . '</a>'
];
?>
<?= \humhub\modules\custom_pages\widgets\SnippetContent::widget([
    'model' => $model,
    'content' => humhub\widgets\MarkdownView::widget(['markdown' => $model->getPageContent()]),
    'navigation' => $navigation
]); ?>