<?php

use humhub\modules\custom_pages\modules\template\assets\SourceEditorAsset;
use humhub\modules\custom_pages\modules\template\models\Template;

/* @var Template $template */

SourceEditorAsset::register($this);

$class = ($template->isLayout()) ? 'prview-layout' : 'priview-container';
?>

<div id="templatePageRoot" data-ui-widget="custom_pages.template.source.TemplateSourcePreview" class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="clearfix">
                <button data-action-click="update" 
                        data-action-url="<?= \yii\helpers\Url::to(['preview', 'id' => $template->id]) ?>" style="margin-left:5px;" class="btn btn-primary btn-md pull-right" role="button" data-ui-loader>
                    <?= Yii::t('CustomPagesModule.template', 'Update'); ?>
                </button>  
                <button data-action-click="switchMode" class="btn btn-success btn-md pull-right">
                    <?= Yii::t('CustomPagesModule.template', 'Display Empty Content'); ?>
                </button>
            </div>
            <br />
            <div id="stage">
                <div id="nonEditModePreview" style="<?= ($editView) ? 'display:none;' : '' ?>"  class="preview <?= $class ?>">
                    <?= $template->render() ?>
                </div>
                <div id="editModePreview" style="<?= ($editView) ? '' : 'display:none;' ?>" class="preview <?= $class ?>">
                    <?= $template->render() ?>
                </div>
            </div>
        </div>
    </div>
</div>
