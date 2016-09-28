<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */
use yii\helpers\Url;

\humhub\modules\custom_pages\SwitchAssetBundle::register($this);
\humhub\modules\custom_pages\Assets::register($this);

$sguid = Yii::$app->request->get('sguid');

$editUrl = Url::to(['/custom_pages/template/layout-admin/edit-source', 'id' => $templateInstance->template_id, 'sguid' => $sguid]);
$editMultipleUrl = Url::to(['/custom_pages/template/owner-content/edit-multiple', 'id' => $templateInstance->id, 'sguid' => $sguid]);
$editOnUrl = Url::to(['view', 'id' => $pageId, 'editMode' => true, 'sguid' => $sguid]);
$editOffUrl = Url::to(['view', 'id' => $pageId, 'editMode' => false, 'sguid' => $sguid]);

?>

<?php if ($canEdit) : ?>
    <div style="position:fixed;right:5px;top:105px;z-index:1028">
        <input id="templatEditMode" type="checkbox" />
        <div id="templateEditMenu">
            <?php if ($editMode && !Yii::$app->user->isGuest && Yii::$app->user->getIdentity()->isSystemAdmin()) : ?>
                <a style="width:100%" target="_blank" class="btn btn-primary btn-xs tt editTemplateElement"  href="<?= $editUrl ?>">
                    <?= Yii::t('CustomPagesModule.views_view_template', 'Edit Template') ?>
                </a><br />
            <?php endif; ?>
            <?php if ($editMode): ?>
                <a id="editAllElements" style="width:100%" class="btn btn-primary btn-xs tt editTemplateElement"  href="<?= $editMultipleUrl ?>">
                    <?= Yii::t('CustomPagesModule.views_view_template', 'Edit Elements') ?>
                </a>
            <?php endif; ?>
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

    $('#templatEditMode').on('switchChange.bootstrapSwitch', function (event, state) {
        if (state) {
            window.location.href = '<?= $editOnUrl ?>';
        } else {
            window.location.href = '<?= $editOffUrl ?>';
        }
    });

</script>
