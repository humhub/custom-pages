<?php

use humhub\helpers\Html;
use humhub\modules\custom_pages\models\CustomPage;
use humhub\modules\custom_pages\widgets\SnippetContent;
use humhub\widgets\bootstrap\Link;

/* @var $model CustomPage */
/* @var $canEdit bool */

$iframeId = 'iframesnippet-' . $model->id;

$navigation = !$canEdit ? [] : [
    Link::to(Yii::t('CustomPagesModule.base', 'Edit'), $model->getEditUrl())
        ->icon('pencil')
        ->cssClass(['btn', 'dropdown-item']),
];
?>

<?= SnippetContent::widget([
    'model' => $model,
    'content' => '<iframe id="' . $iframeId . '"'
            . ' style="border:0px;width:100%;"'
            . ' src="' . Html::encode($model->getPageContent()) . '"'
            . ' aria-label="' . Html::encode($model->title) . '"'
            . ($model->iframe_attrs ? ' ' . $model->iframe_attrs : '')
        . '></iframe>',
    'navigation' => $navigation,
]) ?>

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
