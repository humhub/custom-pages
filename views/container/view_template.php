<?php

use yii\helpers\Url;
use humhub\modules\custom_pages\modules\template\widgets\InlineEditorEditMenu;

humhub\assets\Select2ExtensionAsset::register($this);

if($editMode) {
    \humhub\modules\custom_pages\InlineEditAsset::register($this);
}
?>

<div id="templatePageRoot" data-page-template-id="<?= $templateInstance->id ?>">
    <div class="panel panel-default">
        <div class="panel-body">
            <?php echo $html; ?>
        </div>
    </div>
</div>

<?php if ($canEdit && $editMode): ?>
    <script>
        <?php $guid = Yii::$app->request->get('sguid'); ?>
        // See inlineEditor.js
        var editConfig = {
            $templatePageRoot: $('#templatePageRoot'),
            editTemplateText: '<?= Yii::t('CustomPagesModule.views_view_template', 'Edit Template') ?>',
            toggleOnText: '<?= Yii::t('CustomPagesModule.base', 'On') ?>',
            toggleOffText: '<?= Yii::t('CustomPagesModule.base', 'Off') ?>',

            elementEditUrl: '<?= Url::to(['/custom_pages/template/owner-content/edit', 'sguid' => $guid]) ?>',
            elementDeleteUrl: '<?= Url::to(['/custom_pages/template/owner-content/delete', 'sguid' => $guid]) ?>',

            createContainerUrl: '<?= Url::to(['/custom_pages/template/container-content/create-container', 'sguid' => $guid]) ?>',

            itemDeleteUrl: '<?= Url::to(['/custom_pages/template/container-content/delete-item', 'sguid' => $guid]) ?>',
            itemEditUrl: '<?= Url::to(['/custom_pages/template/container-content/edit-item', 'sguid' => $guid]) ?>',
            itemAddUrl: '<?= Url::to(['/custom_pages/template/container-content/add-item', 'sguid' => $guid]) ?>',
            itemMoveUrl: '<?= Url::to(['/custom_pages/template/container-content/move-item', 'sguid' => $guid]) ?>',
        };
    </script>
<?php endif; ?>
