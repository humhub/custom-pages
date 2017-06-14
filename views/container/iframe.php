<?php
$cssClass = ($page->hasAttribute('cssClass') && !empty($page->cssClass)) ? $page->cssClass : 'custom-pages-page';
?>

<iframe class="<?= $cssClass ?>" id="iframepage" style="width:100%; height: 100%;" src="<?php echo \yii\helpers\Html::encode($url); ?>"></iframe>

<style>
    #iframepage {
        border: none;
        margin-top: 0px;
        background: url('<?= Yii::$app->moduleManager->getModule('custom_pages')->getPublishedUrl('/loader.gif'); ?>') center center no-repeat;
    }
</style>

<script>
    function setSize() {
        $('#iframepage').css('height', (window.innerHeight - $('#iframepage').position().top - 15) + 'px');
    }

    window.onresize = function (evt) {
        setSize();
    };

    debugger;
    $(document).on('humhub:ready', function () {
        debugger;
        $('#iframepage').load(function () {
            setSize();
        });
    });
</script>
