<?php
use yii\helpers\Url;

$iframeId = 'iframesnippet-' . $model->id;

if ($contentContainer != null) {
    $editUrl = $contentContainer->createUrl('/custom_pages/container-snippet/edit', ['id' => $model->id]);
} else {
    $editUrl = Url::to(['/custom_pages/snippet/edit', 'id' => $model->id]);
}

$navigation = (!$canEdit) ? [] : [
    '<a href="'.$editUrl.'" class="panel-collapse"><i class="fa fa-pencil"></i>' . Yii::t('CustomPagesModule.base', 'Edit') . '</a>'
];
?>

<?=
\humhub\modules\custom_pages\widgets\SnippetContent::widget([
    'model' => $model,
    'content' => '<iframe id="' . $iframeId . '" style="border:0px;width:100%;" src="' . \yii\helpers\Html::encode($model->getPageContent()) . '"></iframe>',
    'navigation' => $navigation
]);
?>

<style>
    #<?= $iframeId ?> {
        border: none;
        background: url('<?= Yii::$app->moduleManager->getModule('custom_pages')->getPublishedUrl('/loader.gif'); ?>') center center no-repeat;
    }
</style>

<script>
    var $frame = $('#<?= $iframeId ?>');
    $frame.on('load', function () {
        var height = $(this.contentWindow.document.body).outerHeight() + 20;
        $(this).height(height);
    })
</script>
