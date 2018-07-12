<?php

use yii\helpers\Html;
use humhub\modules\custom_pages\modules\template\widgets\TemplatePage;
?>
<div>
    <?php TemplatePage::begin(['page' => $snippet, 'canEdit' => true, 'editMode' => true, 'contentContainer' => $contentContainer]) ?>
    <div class="panel panel default">
        <div class="panel-heading">
            <?= Yii::t('CustomPagesModule.base', '<strong>Edit</strong> snippet'); ?>
        </div>
        <div class="panel-body">
            <a href="<?= $contentContainer->createUrl('/space/space/index') ?>" class="btn btn-default pull-right" data-ui-loader>
                <i class="fa fa-arrow-left"></i> <?= Yii::t('CustomPagesModule.base', 'Back to space'); ?>
            </a>
            <div class="row">
                <div class="col-md-4"></div>
                <div class="col-md-4">
                    <div class="panel panel-default custom-snippet">
                        <div class="panel-heading">
                            <i class="fa <?= Html::encode($snippet->icon); ?>"></i> <?= Html::encode($snippet->title) ?>
                            <a id="snippet-config-button" href="<?= $contentContainer->createUrl('edit', ['id' => $snippet->id]) ?>" title="<?= Yii::t('CustomPagesModule.base', 'Configuration'); ?>" target="_blank" class="pull-right">
                                <i class="fa fa-pencil"></i>
                            </a>
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
    <?php TemplatePage::end() ?>
</div>
