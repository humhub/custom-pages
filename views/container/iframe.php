<?php
$cssClass = ($page->hasAttribute('cssClass') && !empty($page->cssClass)) ? $page->cssClass : 'custom-pages-page';
?>

<iframe class="<?= $cssClass ?>" id="iframepage" style="width:100%; height: 100%;" src="<?php echo \yii\helpers\Html::encode($url); ?>"></iframe>

<style>
    #iframepage {
        border: none;
        margin-top: 0;
        background: url('<?= Yii::$app->moduleManager->getModule('custom_pages')->getPublishedUrl('/loader.gif'); ?>') center center no-repeat;
    }
</style>

<script>
    function setSize() {
        $('#iframepage').css( {
            height: (window.innerHeight - $('#iframepage').position().top - 15) + 'px',
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
</script>
