<?php

// save url to standard loader graphic
$loader_url = Yii::app()->baseUrl . "/img/loader.gif";

// check if there is a themed loader graphic and replace the url
if (file_exists(Yii::getPathOfAlias('webroot') . "/themes/" . Yii::app()->theme->name . "/img/loader.gif")) {
    $loader_url = Yii::app()->theme->baseUrl . "/img/loader.gif";
}

?>

<?php if ($navigationClass == CustomPage::NAV_CLASS_ACCOUNTNAV): ?>

    <iframe id="iframepage" style="width:100%; height: 400px;" src="<?php echo $url; ?>"></iframe>

    <style>
        #iframepage {
            border: none;
            background: url('<?php echo $loader_url; ?>') center center no-repeat;
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

    <iframe id="iframepage" style="width:100%;height:400px" src="<?php echo $url; ?>"></iframe>

    <style>
        #iframepage {
            position: absolute;
            left: 0;
            top: 98px;
            border: none;
            background: url('<?php echo $loader_url; ?>') center center no-repeat;
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
