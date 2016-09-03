<?php

use yii\helpers\Url;

humhub\assets\Select2ExtensionAsset::register($this);

if ($editMode) {
    \humhub\modules\custom_pages\InlineEditAsset::register($this);
}
?>

<div id="templatePageRoot">
    <div class="panel panel-default">
        <div class="panel-body">
            <?php echo $html; ?>
        </div>
    </div>
</div>

<?php if ($canEdit && $editMode): ?>
    <?php $contentContainer = property_exists(Yii::$app->controller, 'contentContainer') ? Yii::$app->controller->contentContainer : null; ?>

    <script>
        // See inlineEditor.js
        var editConfig = {
            $templatePageRoot: $('#templatePageRoot'),
            editTemplateText: '<?= Yii::t('CustomPagesModule.views_view_template', 'Edit Template') ?>',
            toggleOnText: '<?= Yii::t('CustomPagesModule.base', 'On') ?>',
            toggleOffText: '<?= Yii::t('CustomPagesModule.base', 'Off') ?>',
            elementEditUrl: '<?= $contentContainer->createUrl('/custom_pages/template/owner-content/edit') ?>',
            elementDeleteUrl: '<?= $contentContainer->createUrl('/custom_pages/template/owner-content/delete') ?>',
            createContainerUrl: '<?= $contentContainer->createUrl('/custom_pages/template/container-content/create-container') ?>',
            itemDeleteUrl: '<?= $contentContainer->createUrl('/custom_pages/template/container-content/delete-item') ?>',
            itemEditUrl: '<?= $contentContainer->createUrl('/custom_pages/template/container-content/edit-item') ?>',
            itemAddUrl: '<?= $contentContainer->createUrl('/custom_pages/template/container-content/add-item') ?>',
            itemMoveUrl: '<?= $contentContainer->createUrl('/custom_pages/template/container-content/move-item') ?>',
        };
    </script>
<?php endif; ?>
