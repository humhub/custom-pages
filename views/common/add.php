<?php

use yii\helpers\Html;
use yii\helpers\Url;
use humhub\modules\custom_pages\components\Container;
use humhub\modules\custom_pages\widgets\AddContentTypeRow;

/* @var $model humhub\modules\custom_pages\models\AddPageForm */
/* @var $subNav string */


$indexUrl = Url::to(['index' , 'sguid' => Yii::$app->request->get('sguid')]);

?>
<div class="panel panel-default">
    <div class="panel-heading"><?php echo Yii::t('CustomPagesModule.base', '<strong>Custom</strong> Pages'); ?></div>

    <?= $subNav ?>

    <div class="panel-body">
        <div class="clearfix">
            <?php echo Html::a('<i class="fa fa-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;' . Yii::t('base', 'Back to overview'), $indexUrl, ['data-ui-loader' => '', 'class' => 'btn btn-default pull-right']); ?>
            <h4><?= Yii::t('CustomPagesModule.views_admin_add', 'Add new {pageType}', ['pageType' => $model->getPageLabel()]) ?></h4>
            <div class="help-block">
                <?= Yii::t('CustomPagesModule.base', 'Please choose one of the following content types. The content type defines how your content is embeded to your site.') ?>  
            </div>
        </div>

        <div class="grid-view">
            <table class="table table-hover">
                <tbody>
                    <?=
                    AddContentTypeRow::widget([
                        'type' => Container::TYPE_MARKDOWN,
                        'label' => Yii::t('CustomPagesModule.base', 'MarkDown'),
                        'description' => Yii::t('CustomPagesModule.base', 'Allows you to add content in MarkDown syntax.'),
                        'hide' => !$model->isAllowedType(Container::TYPE_MARKDOWN)
                    ])
                    ?>

                    <?=
                    AddContentTypeRow::widget([
                        'type' => Container::TYPE_LINK,
                        'label' => Yii::t('CustomPagesModule.base', 'Link'),
                        'description' => Yii::t('CustomPagesModule.base', 'Will redirect requests to a given (relative or absolute) url.'),
                        'hide' => !$model->isAllowedType(Container::TYPE_LINK)
                    ])
                    ?>

                    <?=
                    AddContentTypeRow::widget([
                        'type' => Container::TYPE_IFRAME,
                        'label' => Yii::t('CustomPagesModule.base', 'Iframe'),
                        'description' => Yii::t('CustomPagesModule.base', 'Will embed the the result of a given url as an iframe element.'),
                        'hide' => !$model->isAllowedType(Container::TYPE_IFRAME)
                    ])
                    ?>


                    <?= AddContentTypeRow::widget([
                        'type' => Container::TYPE_TEMPLATE,
                        'label' => Yii::t('CustomPagesModule.base', 'Template'),
                        'description' => Yii::t('CustomPagesModule.base', 'Templates allow you to define combinable page fragments with inline edit functionality.'),
                        'hide' => !$model->isAllowedType(Container::TYPE_TEMPLATE),
                        'disabled' => !$model->showTemplateType()
                    ]) ?>

                    <?=
                    AddContentTypeRow::widget([
                        'type' => Container::TYPE_HTML,
                        'label' => Yii::t('CustomPagesModule.base', 'Html'),
                        'description' => Yii::t('CustomPagesModule.base', 'Adds plain HTML content to your site.'),
                        'hide' => !$model->isAllowedType(Container::TYPE_HTML)
                    ])
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>