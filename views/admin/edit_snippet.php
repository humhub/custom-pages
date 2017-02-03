<?php

use yii\helpers\Url;
use yii\helpers\Html;
use humhub\modules\custom_pages\models\Snippet;

if(version_compare(Yii::$app->version, '1.2', '<')) {
    \humhub\assets\Select2ExtensionAsset::register($this);
}
\humhub\modules\custom_pages\InlineEditAsset::register($this);

$backUrl = ($snippet->sidebar == Snippet::SIDEBAR_DASHBOARD) ? Url::to(['/dashboard/dashboard']) :  Url::to(['/directory/directory']);
$backText = ($snippet->sidebar == Snippet::SIDEBAR_DASHBOARD) ?Yii::t('CustomPagesModule.base', 'Back to dashboard') : Yii::t('CustomPagesModule.base', 'Back to directory');
$editUrl = Url::to(['edit', 'id' => $snippet->id]);

?>

<div id="templatePageRoot" class="col-md-12 layout-content-container">
    <div class="panel panel default">
        <div class="panel-heading">
            <?= Yii::t('CustomPagesModule.base', '<strong>Edit</strong> snippet'); ?>
        </div>
        <div class="panel-body">
            <a href="<?= $backUrl ?>" class="btn btn-default pull-right" data-ui-loader><i class="fa fa-arrow-left"></i> <?= $backText ?></a>
            <div class="row">
                <div class="col-md-3"></div>
                <div class="col-md-6">
                    <div class="panel panel-default custom-snippet">
                        <div class="panel-heading">
                            <i class="fa <?= Html::encode($snippet->icon) ?>"></i> <?= Html::encode($snippet->title) ?>
                            <a id="snippet-config-button" href="<?= $editUrl ?>" title="<?= Yii::t('CustomPagesModule.base', 'Configuration'); ?>" target="_blank" class="pull-right"><i class="fa fa-pencil"></i></a>
                        </div>
                        <div class="panel-body">
                            <?php echo $html; ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-3"></div>
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
        elementEditUrl: '<?= Url::to(['/custom_pages/template/owner-content/edit']) ?>',
        elementDeleteUrl: '<?= Url::to(['/custom_pages/template/owner-content/delete']) ?>',
        createContainerUrl: '<?= Url::to(['/custom_pages/template/container-content/create-container']) ?>',
        itemDeleteUrl: '<?= Url::to(['/custom_pages/template/container-content/delete-item']) ?>',
        itemEditUrl: '<?= Url::to(['/custom_pages/template/container-content/edit-item']) ?>',
        itemAddUrl: '<?= Url::to(['/custom_pages/template/container-content/add-item']) ?>',
        itemMoveUrl: '<?= Url::to(['/custom_pages/template/container-content/move-item']) ?>',
    };
</script>
