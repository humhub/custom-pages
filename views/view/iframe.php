<?php
use humhub\modules\custom_pages\models\Page;

$cssClass = ($page->hasAttribute('cssClass') && !empty($page->cssClass)) ? $page->cssClass : 'custom-pages-page';
?>

<?php if ($navigationClass == Page::NAV_CLASS_ACCOUNTNAV): ?>

    <iframe class="<?= $cssClass ?>" id="iframepage" style="width:100%; height: 400px;" src="<?php echo $url; ?>"></iframe>

    <style>
        #iframepage {
            border: none;
            background: url('<?= Yii::$app->moduleManager->getModule('custom_pages')->getPublishedUrl('/loader.gif'); ?>') center center no-repeat;
        }
    </style>

    <script>
        window.onload = function (evt) {
            setSize();
        }
        window.onresize = function (evt) {
            setSize();
        }

        function setSize() {

            $('#iframepage').css('height', window.innerHeight - 170 + 'px');
        }
    </script>


<?php else: ?>

    <iframe class="<?= $cssClass ?>" id="iframepage" style="width:100%;height: 400px" src="<?php echo $url; ?>"></iframe>

    <style>
        #iframepage {
            position: absolute;
            left: 0;
            top: 98px;
            border: none;
            background: url('<?= Yii::$app->moduleManager->getModule('custom_pages')->getPublishedUrl('/loader.gif'); ?>') center center no-repeat;
        }
    </style>


    <script>
        window.onload = function (evt) {
            setSize();
        }
        window.onresize = function (evt) {
            setSize();
        }

        function setSize() {

            $('#iframepage').css('height', window.innerHeight - 100 + 'px');
            $('#iframepage').css('width', jQuery('body').outerWidth() - 1 + 'px');
        }
    </script>
<?php endif; ?>
