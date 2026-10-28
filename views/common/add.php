<?php

use humhub\helpers\Html;
use humhub\modules\custom_pages\helpers\Url;
use humhub\modules\custom_pages\types\ContentType;
use humhub\modules\custom_pages\widgets\AddContentTypeRow;
use humhub\widgets\ContentCard;

/* @var $model \humhub\modules\custom_pages\models\forms\AddPageForm */
/* @var $target \humhub\modules\custom_pages\models\Target */
/* @var $pageType string */

?>
<?php ContentCard::begin([
    'title' => Yii::t('CustomPagesModule.view', 'Add new {pageType}', ['pageType' => $model->getPageLabel()]) . ': ' . $target->name,
    'description' => Html::encode(Yii::t('CustomPagesModule.base', 'Please choose one of the following content types. The content type defines how your content is embedded to your site.')),
    'backUrl' => Url::toOverview($pageType, $target->container),
    'backLabel' => Yii::t('CustomPagesModule.base', 'Back to overview'),
]) ?>
    <div class="grid-view">
        <table class="table table-hover">
            <tbody>
            <?php foreach (ContentType::getContentTypes() as $contentType) : ?>
                <?= AddContentTypeRow::widget([
                    'contentType' => $contentType,
                    'target' => $target,
                    'pageType' => $pageType,
                    'hide' => !$model->isAllowedType($contentType),
                    'disabled' => $model->isDisabledType($contentType),
                ]) ?>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php ContentCard::end() ?>
