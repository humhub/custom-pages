<?php
use \humhub\modules\custom_pages\models\Page;
use yii\helpers\Html;

$cssClass = ($page->hasAttribute('cssClass') && !empty($page->cssClass)) ? $page->cssClass : 'custom-pages-page';
$margin = $navigationClass == Page::NAV_CLASS_TOPNAV ? -15 : 0;
?>

<style>
    #iframepage {
        border: none;
        <?= $margin ? 'margin-top:'.$margin.'px;' : ''?>        
        background: url('<?= Yii::$app->moduleManager->getModule('custom_pages')->getPublishedUrl('/loader.gif'); ?>') center center no-repeat;
    }
</style>

<iframe class="<?= Html::encode($cssClass) ?>" id="iframepage" style="width:100%;height: 100%" src="<?= Html::encode($url) ?>"></iframe>


<script>
    function setSize() {
        $('#iframepage').css( {
            height: (window.innerHeight - $('#iframepage').position().top - 15) + 'px',
            background: 'inherit'
        });
    }
    
    // execute setSize in the beginning, else dynamically loaded content in the 
    // Iframe gets the wrong size to work with
    setSize();

    window.onresize = function (evt) {
        setSize();
    };

    $(document).on('humhub:ready', function () {
        $('#iframepage').on('load',function () {
            setSize();
        });
    });

</script>
