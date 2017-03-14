<?php

use yii\helpers\Html;

if(version_compare(Yii::$app->version, '1.2', '<')) {   
    \humhub\assets\Select2ExtensionAsset::register($this);
}
\humhub\modules\custom_pages\assets\TemplateEditorAsset::register($this);
?>

<div id="templatePageRoot" class="col-md-12 layout-content-container">
    <div class="panel panel default">
        <div class="panel-heading">
            <?= Yii::t('CustomPagesModule.base', '<strong>Edit</strong> snippet'); ?>
        </div>
        <div class="panel-body">
            <a href="<?= $contentContainer->createUrl('/space/space/index') ?>" class="btn btn-default pull-right" data-ui-loader><i class="fa fa-arrow-left"></i> <?= Yii::t('CustomPagesModule.base', 'Back to space'); ?></a>
            <div class="row">
                <div class="col-md-4"></div>
                <div class="col-md-4">
                    <div class="panel panel-default custom-snippet">
                        <div class="panel-heading">
                            <i class="fa <?= Html::encode($snippet->icon); ?>"></i> <?= Html::encode($snippet->title) ?>
                            <a id="snippet-config-button" href="<?= $contentContainer->createUrl('edit', ['id' => $snippet->id]) ?>" title="<?= Yii::t('CustomPagesModule.base', 'Configuration'); ?>" target="_blank" class="pull-right"><i class="fa fa-pencil"></i></a>
                        </div>
                        <div class="panel-body">
                            <?php echo $html; ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-4"></div>
            </div>
        </div>
    </div>
</div>

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
