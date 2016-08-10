<?php

humhub\modules\custom_pages\Assets::register($this);
$class = ($template->isLayout()) ? 'prview-layout' : 'priview-container';

?>

<div id="templatePageRoot" class="container" data-page-template-id="11">
    <div class="row">
        <div class="col-md-12">
            <div class="clearfix">
                <a id="update" style="margin-left:5px;" href="#" class="btn btn-primary btn-lg pull-right" role="button" data-ui-loader><?= Yii::t('CustomPagesModule.modules_template_views_admin_preview', 'Update'); ?></a>
        
                <a id="switchMode" href="#" class="btn btn-success btn-lg pull-right" role="button"><?= Yii::t('CustomPagesModule.modules_template_views_admin_preview', 'Display Empty Content'); ?></a>
            </div>
            <br />
            <div id="stage">
                <div id="nonEditModePreview" style="<?= ($editView) ? 'display:none;' : '' ?>"  class="preview <?= $class ?>">
                    <?= $template->render(null, false); ?>
                </div>
                <div id="editModePreview" style="<?= ($editView) ? '' : 'display:none;' ?>" class="preview <?= $class ?>">
                    <?= $template->render(null, true); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $('#update').on('click', function(evt) {
        var $this = $(this);
        $.ajax('<?= \yii\helpers\Url::to(['preview', 'id' => $template->id]) ?>', {
            method: 'GET',
            data: {
                'reload': '1',
                'editView': $('#editModePreview').is(':visible') ? '1' : '0'
            },
            success: function(result) {
                var $result = $(result);
                $result.find('#stage').hide();
                $('#templatePageRoot').replaceWith($result);
                $result.find('#stage').fadeIn('fast');
               
            }
        });
    });
    
    $('#switchMode').on('click', function(evt) {
        $(this).toggleClass('active');
        evt.preventDefault();
        $('#nonEditModePreview, #editModePreview').toggle();
    });
</script>



