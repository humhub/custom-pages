<?php

use humhub\libs\Html;

$cssClass = ($page->hasAttribute('cssClass') && !empty($page->cssClass)) ? $page->cssClass : 'custom-pages-page';
$allow_attribute = ($page->hasAttribute('allow_attribute') && !empty($page->allow_attribute)) ? $page->allow_attribute : '';
?>

<iframe class="<?= Html::encode($cssClass) ?>" id="iframepage" style="width:100%; height: 100%; min-height: 400px;"
        src="<?= Html::encode($url); ?>" <?= !empty($allow_attribute) ? 'allow="' . Html::encode($allow_attribute) . '"' : ''; ?>></iframe>

<style>
    #iframepage {
        border: none;
        margin-top: 0;
        background: url('<?= Yii::$app->moduleManager->getModule('custom_pages')->getPublishedUrl('/loader.gif'); ?>') center center no-repeat;
    }
</style>

<?= Html::script(<<<JS
    function setSize() {
        $('#iframepage').css( {
            height: ($(window).height() - $('#layout-content').position().top - 15) + 'px',
            background: 'inherit'
        });
    }

    window.onresize = function (evt) {
        setSize();
    };

    $(document).on('humhub:ready', function () {
        $('#iframepage').on('load', function () {
            setSize();
        });
    });
JS
) ?>
