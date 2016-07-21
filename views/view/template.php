<?php
    use yii\helpers\Url;
?>
<div id="templatePageRoot" class="container" data-page-template-id="<?= $pageTemplate->id ?>">
    <div class="row">
        <div class="col-md-12">

            <?php echo $html; ?>

        </div>
    </div>
</div>

<?php if(!Yii::$app->user->isGuest && Yii::$app->user->getIdentity()->isSystemAdmin()): ?>
<?php \humhub\modules\custom_pages\CkEditorAssetBundle::register($this); ?>

<script>
    var $editButton;
    $('[data-template-block]').hover(function() {
        var $this = $(this);
        if($editButton) {
            $editButton.remove();
            $editButton = null;
        }
        
        var blockName = $this.data('template-block');
        var tmplId = $('#templatePageRoot').data('page-template-id');
        
        $editButton = $('<div style="position:absolute;"><a class="btn btn-primary btn-xs tt editBlockButton" href="#"><i class="fa fa-pencil"></i></a></div>');
        
        var offset = $this.offset();
        $editButton.css({
            'top': offset.top,
            'left': offset.left + $this.outerWidth() - 20
        });
        
        
        $editButton.on('click', function() {
            $.ajax('<?= Url::to(['/custom_pages/template/edit-page-template-block']) ?>', {
                dataType: 'json',
                data: {
                    pageTemplateId: tmplId,
                    name: blockName
                },
                beforeSend: function () {
                    //setModalLoader();
                },
                success: function (json) {
                    $('#globalModal').html(json.content);
                    $('#globalModal').modal('show');
                },
                error: function () {

                }
            });
        });
        
        $('html').append($editButton);
    });
    
    $(document).on('templateBlockEditSuccess', function(evt, json) {
    debugger;
        $('#globalModal').modal('hide');
        $('[data-template-block="'+json.name+'"]').replaceWith(json.content);
    });
</script>
<?php endif; ?>
