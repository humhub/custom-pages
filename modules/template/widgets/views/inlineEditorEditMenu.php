<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use yii\helpers\Url;

\humhub\modules\custom_pages\SwitchAssetBundle::register($this);
\humhub\modules\custom_pages\Assets::register($this);
?>

<?php if ($canEdit) : ?>
    <?php $sguid = Yii::$app->request->get('sguid'); ?>
    <div style="position:fixed;right:5px;top:105px;z-index:1028">
        <input id="templatEditMode" type="checkbox" />
        <div id="templateEditMenu">
            <?php if ($editMode && !Yii::$app->user->isGuest && Yii::$app->user->getIdentity()->isSystemAdmin()) : ?>
                <a style="width:100%" target="_blank" class="btn btn-primary btn-xs tt editTemplateElement"  href="<?= Url::to(['/custom_pages/template/layout-admin/edit-source', 'id' => $templateInstance->template_id, 'sguid' => $sguid]) ?>">
                    <?= Yii::t('CustomPagesModule.views_view_template', 'Edit Template') ?>
                </a><br />
            <?php endif; ?>
            <?php if ($editMode): ?>
            <a id="editAllElements" style="width:100%" class="btn btn-primary btn-xs tt editTemplateElement"  href="<?= Url::to(['/custom_pages/template/owner-content/edit-multiple', 'id' => $templateInstance->id]) ?>">
                <?= Yii::t('CustomPagesModule.views_view_template', 'Edit Elements') ?>
            </a>
            <?php  endif; ?>
        </div>
    </div>
<?php endif; ?>

<script>
    $('#templatEditMode').bootstrapSwitch({
        'size': 'mini',
        'state':<?= ($editMode) ? 'true' : 'false' ?>,
        'onText': '<?= Yii::t('CustomPagesModule.base', 'Edit On') ?>',
        'offText': '<?= Yii::t('CustomPagesModule.base', 'Edit Off') ?>'
    });

    <?php 
        $guid = Yii::$app->request->get('sguid');
    ?>

    $('#templatEditMode').on('switchChange.bootstrapSwitch', function (event, state) {
        if (state) {
            window.location.href = '<?= Url::to(['view', 'id' => $pageId, 'editMode' => true, 'sguid' => $guid]); ?>';
        } else {
            window.location.href = '<?= Url::to(['view', 'id' => $pageId, 'editMode' => false, 'sguid' => $guid]); ?>';
        }
    });
    
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
