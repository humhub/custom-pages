<?php
use humhub\libs\Html;use humhub\modules\custom_pages\widgets\SnippetContent;

/* @var $model \humhub\modules\custom_pages\models\CustomContentContainer */
/* @var $canEdit bool */

$iframeId = 'iframesnippet-' . $model->id;

$navigation = (!$canEdit) ? [] : [
    '<a href="'.$model->getEditUrl().'"><i class="fa fa-pencil"></i>' . Yii::t('CustomPagesModule.base', 'Edit') . '</a>'
];
?>

<?=
SnippetContent::widget([
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

<?= Html::beginTag('script') ?>
    var $frame = $('#<?= $iframeId ?>');
    $frame.on('load', function () {
        var height = $(this.contentWindow.document.body).outerHeight() + 20;
        $(this).height(height);
    })
<?= Html::endTag('script') ?>
