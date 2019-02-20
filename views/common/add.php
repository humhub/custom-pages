<?php

use humhub\modules\custom_pages\helpers\Url;
use humhub\modules\custom_pages\models\ContentType;
use humhub\modules\custom_pages\widgets\AddContentTypeRow;
use humhub\widgets\Button;

/* @var $model \humhub\modules\custom_pages\models\forms\AddPageForm */
/* @var $target \humhub\modules\custom_pages\models\Target */
/* @var $subNav string */
/* @var $pageType string */

?>
<div class="panel panel-default">
    <div class="panel-heading"><?= Yii::t('CustomPagesModule.base', '<strong>Custom</strong> Pages'); ?></div>

    <?= $subNav ?>

    <div class="panel-body">
        <div class="clearfix">
            <?= Button::back(Url::toOverview($pageType, $model->target->container), Yii::t('CustomPagesModule.base', 'Back to overview'))->sm(); ?>
            <h4><?= Yii::t('CustomPagesModule.views_admin_add', 'Add new {pageType}', ['pageType' => $model->getPageLabel()]) ?></h4>
            <div class="help-block">
                <?= Yii::t('CustomPagesModule.base', 'Please choose one of the following content types. The content type defines how your content is embedded to your site.') ?>
            </div>
        </div>

        <div class="grid-view">
            <table class="table table-hover">
                <tbody>

                <?php foreach (ContentType::getContentTypes() as $contentType) : ?>
                    <?= AddContentTypeRow::widget([
                        'contentType' => $contentType,
                        'target' => $target,
                        'pageType' => $pageType,
                        'hide' => !$model->isAllowedType($contentType),
                        'disabled' => $model->isDisabledType($contentType)
                    ]); ?>
                <?php endforeach; ?>

                </tbody>
            </table>
        </div>
    </div>
</div>