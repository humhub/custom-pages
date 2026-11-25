<?php

use yii\helpers\Html;
use humhub\modules\custom_pages\modules\template\widgets\TemplatePage;
?>
<div>
    <?php TemplatePage::begin(['page' => $snippet]) ?>
    <div class="card content-card">
        <div class="card-header">
            <div class="card-title"><?= Yii::t('CustomPagesModule.base', '<strong>Edit</strong> snippet'); ?></div>
        </div>
        <div class="card-body">
            <a href="<?= $contentContainer->createUrl('/space/space/index') ?>" class="btn btn-light float-end" data-ui-loader>
                <i class="ti ti-arrow-left"></i> <?= Yii::t('CustomPagesModule.base', 'Back to space'); ?>
            </a>
            <div class="row">
                <div class="col-md-4"></div>
                <div class="col-md-4">
                    <div class="card content-card custom-snippet">
                        <div class="card-header">
                            <div class="card-title"><i class="fa <?= Html::encode($snippet->icon); ?>"></i> <?= Html::encode($snippet->title) ?></div>
                            <div class="card-header-actions">
                                <a id="snippet-config-button" href="<?= $contentContainer->createUrl('edit', ['id' => $snippet->id]) ?>" title="<?= Yii::t('CustomPagesModule.base', 'Configuration'); ?>" target="_blank">
                                    <i class="ti ti-pencil"></i>
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
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
