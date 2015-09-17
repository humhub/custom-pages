<iframe id="iframepage" style="width:100%; height: 400px;" src="<?php echo \yii\helpers\Html::encode($url); ?>"></iframe>

<style>
    #iframepage {
        border: none;
        background: url('<?php echo Yii::getAlias("@web/img/loader.gif"); ?>') center center no-repeat;
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
