<?php
$cssClass = ($page->hasAttribute('cssClass') && !empty($page->cssClass)) ? $page->cssClass : 'custom-pages-page';
?>

<style>
    #iframepage {
        border: none;
        background: url('<?= Yii::$app->moduleManager->getModule('custom_pages')->getPublishedUrl('/loader.gif'); ?>') center center no-repeat;
    }
</style>

<iframe class="<?= $cssClass ?>" id="iframepage" style="width:100%;height: 100%" src="<?php echo $url; ?>"></iframe>    


<script>
    function setSize() {
        $('#iframepage').css('height', window.innerHeight - $('#iframepage').position().top - 15 + 'px');
    }

    $(document).on('humhub:ready', function () {
        $('#iframepage').load(function () {
            setSize();
        });
    });

</script>
