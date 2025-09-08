<?php

use humhub\modules\custom_pages\helpers\Url;
use humhub\modules\custom_pages\models\CustomPage;
use yii\helpers\Html;
use humhub\modules\custom_pages\modules\template\widgets\TemplatePage;

/* @var $snippet CustomPage */
/* @var $html string */

$backUrl = Url::to(['/dashboard/dashboard']);
$backText = Yii::t('CustomPagesModule.base', 'Back to dashboard');

$editUrl = Url::toEditSnippet($snippet, $snippet->content->container);
?>

<div>
    <?php TemplatePage::begin(['page' => $snippet]) ?>
        <div class="panel panel default">
            <div class="panel-body">
                <a href="<?= $backUrl ?>" class="btn btn-light float-end" data-ui-loader><i class="fa fa-arrow-left"></i> <?= $backText ?></a>
                <div class="row">
                    <div class="col-md-3"></div>
                    <div class="col-md-6">
                        <div class="panel panel-default custom-snippet">
                            <div class="panel-heading">
                                <i class="fa <?= Html::encode($snippet->icon) ?>"></i> <?= Html::encode($snippet->title) ?>
                                <a id="snippet-config-button" href="<?= $editUrl ?>" title="<?= Yii::t('CustomPagesModule.base', 'Configuration'); ?>" target="_blank" class="float-end"><i class="fa fa-pencil"></i></a>
                            </div>
                            <div class="panel-body">
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
