<?php

use yii\helpers\Html;
use yii\helpers\Url;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//echo Html::a('<i class="fa fa-plus"></i> '. Yii::t('SpaceModule.widgets_views_inviteButton', 'Invite'), '#', array('class' => 'btn btn-primary btn-sm', 'data-target' => '#globalModal'));
?>

<?php if ($editMode) : ?>

    <div id="editPageButton" class="btn-group">
        <button type="button" class="btn btn-danger btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fa fa-pencil"></i>
            <?= Yii::t('CustomPagesModule.modules_template_widgets_views_templatePageEditButton', 'Edit Page') ?> <span class="caret"></span>
        </button>
        <ul class="dropdown-menu pull-right">
            <li>
                <a target="_blank"  href="<?= Url::to(['edit', 'id' => $pageId, 'sguid' => $sguid]) ?>">
                    <?= Yii::t('CustomPagesModule.views_view_template', 'Page configuration') ?>
                </a>
            </li>
            <li>
                <a target="_blank"  href="<?= Url::to(['/custom_pages/template/layout-admin/edit-source', 'id' => $templateInstance->template_id, 'sguid' => $sguid]) ?>">
                    <?= Yii::t('CustomPagesModule.views_view_template', 'Edit template') ?>
                </a>
            </li>
            <li>
                <a id="editAllElements" href="<?= Url::to(['/custom_pages/template/owner-content/edit-multiple', 'id' => $templateInstance->id]) ?>">
                    <?= Yii::t('CustomPagesModule.views_view_template', 'Edit elements') ?>
                </a>
            </li>
            <li>
                <a href="<?= Url::to(['view', 'id' => $pageId, 'editMode' => false, 'sguid' => $sguid]); ?>">
                    <?= Yii::t('CustomPagesModule.views_view_template', 'Turn edit off') ?>
                </a>
            </li>
        </ul>
    </div>

<?php else: ?>
    <a id="editPageButton" class="btn btn-danger btn-sm" style="color:#000;" href="<?= Url::to(['view', 'id' => $pageId, 'editMode' => true, 'sguid' => $sguid]); ?>">
        <i class="fa fa-pencil"></i>    
        <?= Yii::t('CustomPagesModule.modules_template_widgets_views_templatePageEditButton', 'Edit Page') ?>
    </a>
<?php endif; ?>

<script>
    $('#editAllElements').on('click', function(evt) {
        evt.preventDefault();
        var url = $(this).attr('href');
        $.ajax(url, {
            type: 'POST',
            dataType: 'json',
            beforeSend: function () {
                setModalLoader();
                $('#globalModal').modal('show');
            },
            success: function (json) {
                $('#globalModal').html(json.content);
            }
        });
    });
    
    $(document).on('templateMultipleElementEditSuccess', function() {
        window.location.href = '<?= Url::to(); ?>';
    });
</script>