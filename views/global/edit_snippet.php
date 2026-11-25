<?php

use humhub\modules\custom_pages\helpers\Url;
use humhub\modules\custom_pages\models\CustomPage;
use yii\helpers\Html;
use humhub\modules\custom_pages\modules\template\widgets\TemplatePage;
use humhub\widgets\Icon;

/* @var $snippet CustomPage */
/* @var $html string */

$backUrl = Url::to(['/dashboard/dashboard']);
$backText = Yii::t('CustomPagesModule.base', 'Back to dashboard');

$editUrl = Url::toEditSnippet($snippet, $snippet->content->container);
?>

<div>
    <?php TemplatePage::begin(['page' => $snippet]) ?>
        <div class="card content-card">
            <div class="card-body">
                <a href="<?= $backUrl ?>" class="btn btn-light float-end" data-ui-loader><i class="ti ti-arrow-left"></i> <?= $backText ?></a>
                <div class="row">
                    <div class="col-md-3"></div>
                    <div class="col-md-6">
                        <div class="card content-card custom-snippet">
                            <div class="card-header">
                                <div class="card-title"><?= $snippet->icon ? Icon::get($snippet->icon) . ' ' : '' ?><?= Html::encode($snippet->title) ?></div>
                                <div class="card-header-actions"><a id="snippet-config-button" href="<?= $editUrl ?>" title="<?= Yii::t('CustomPagesModule.base', 'Configuration'); ?>" target="_blank"><i class="ti ti-pencil"></i></a></div>
                            </div>
                            <div class="card-body">
                                <?= $html; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3"></div>
                </div>
            </div>
        </div>
    <?php TemplatePage::end() ?>
</div>
