<iframe id="iframepage" style="width:100%;height:400px" src="<?php echo $url; ?>"></iframe>

<style>
    #iframepage {
        position: absolute;
        left: 0;
        top: 98px;
        border: none;
        background: url('loader.gif') center center no - repeat;
    }
</style>    


<script>
    window.onload = function(evt) {
        setSize();
    }
    window.onresize = function(evt) {
        setSize();
    }

    function setSize() {

        $('#iframepage').css('height', window.innerHeight - 100 + 'px');
        $('#iframepage').css('width', jQuery('body').outerWidth() - 1 + 'px');
    }
</script>    