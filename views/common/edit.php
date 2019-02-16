<?php

use humhub\modules\custom_pages\widgets\PageIconSelect;
use humhub\modules\ui\form\widgets\Markdown;
use humhub\widgets\MarkdownEditor;
use yii\helpers\Html;
use humhub\modules\custom_pages\helpers\Url;
use yii\widgets\ActiveForm;
use humhub\modules\custom_pages\models\Page;
use humhub\modules\custom_pages\models\ContainerPage;
use humhub\modules\custom_pages\components\Container;
use humhub\modules\custom_pages\models\Snippet;
use humhub\modules\custom_pages\models\ContainerSnippet;
use humhub\widgets\Button;
use humhub\modules\custom_pages\models\LinkType;
use humhub\modules\custom_pages\models\HtmlType;
use humhub\modules\custom_pages\models\TemplateType;
use humhub\modules\custom_pages\models\MarkdownType;
use humhub\modules\custom_pages\models\PhpType;

/** @var  $page \humhub\modules\custom_pages\models\CustomContentContainer */
/** @var  $subNav string */

$indexUrl = Url::to(['index', 'sguid' => null]);
$deleteUrl = Url::to(['delete', 'id' => $page->id, 'sguid' => null]);

$target = $page->getTargetModel();

$contentType = $page->getContentType();

?>
<div class="panel panel-default">
    <div class="panel-heading">
        <?= Yii::t('CustomPagesModule.base', '<strong>Custom</strong> Pages'); ?>
    </div>

    <?= $subNav ?>

    <div class="panel-body">
        <?= Button::back($target->getEditBackUrl(), Yii::t('CustomPagesModule.base', 'Back to overview'))->sm(); ?>

        <h4><?= Yii::t('CustomPagesModule.views_common_edit', 'Configuration'); ?></h4>

        <div class="help-block">
            <?= Yii::t('CustomPagesModule.views_common_edit', 'Here you can configure the general settings of your {label}.', ['label' => $page->getLabel()]) ?>
        </div>

        <?php $form = ActiveForm::begin(); ?>

            <div class="form-group">
                <?= Html::textInput('type', $contentType->getLabel(), ['class' => 'form-control', 'disabled' => '1']); ?>
            </div>

            <div class="form-group">
                <?= Html::textInput('target', $target->name, ['class' => 'form-control', 'disabled' => '1']); ?>
            </div>

            <?= $form->field($page, 'title') ?>

            <?= PageIconSelect::widget(['page' => $page]) ?>

            <?php if ($contentType->isUrlContent()): ?>
                <?= $form->field($page, $page->getPageContentProperty())->textInput(['class' => 'form-control'])->label($page->getAttributeLabel('targetUrl')); ?>
                <div class="help-block">
                    <?= Yii::t('CustomPagesModule.views_common_edit', 'e.g. http://www.example.de') ?>
                </div>
            <?php endif; ?>

            <?php if ($page instanceof Page && $page->hasAttribute('url')) : ?>
                <?= $form->field($page, 'url') ?>
                <div class="help-block">
                    <?= Yii::t('CustomPagesModule.views_common_edit', 'By setting an url shortcut value, you can create a better readable url for your page. If <b>URL Rewriting</b> is enabled on your site, the value \'mypage\' will result in an url \'www.example.de/p/mypage\'.') ?>
                </div>
            <?php endif; ?>

            <?php if (HtmlType::isType($contentType)) : ?>
                <?= $form->field($page, $page->getPageContentProperty())->textarea(['id' => 'html_content', 'class' => 'form-control', 'rows' => '15']); ?>
            <?php elseif (TemplateType::isType($contentType)): ?>
                <?= $form->field($page, 'templateId')->dropDownList($page->getAllowedTemplateSelection(), ['value' => $page->getTemplateId(), 'disabled' => !$page->isNewRecord]) ?>
            <?php elseif (MarkdownType::isType($contentType)) : ?>
                <?= $form->field($page, $page->getPageContentProperty())->textarea(['id' => 'markdownField', 'class' => 'form-control', 'rows' => '15'])->widget(Markdown::class ); ?>
            <?php elseif (PhpType::isType($contentType)): ?>
                <?=  $form->field($page, $page->getPageContentProperty())->dropDownList($page->getAllowedPhpViewFileSelection()) ?>
            <?php endif; ?>

            <?= $form->field($page, 'sort_order')->textInput(); ?>

            <?php if ($page->hasAttribute('cssClass') && LinkType::isType($contentType)) : ?>
                <?= $form->field($page, 'cssClass'); ?>
            <?php endif; ?>

            <?php if ($page->hasAttribute('admin_only')) : ?>
                <?= $form->field($page, 'admin_only')->checkbox() ?>
            <?php endif; ?>

            <?php if ($page->hasAttribute('in_new_window')) : ?>
                <?= $form->field($page, 'in_new_window')->checkbox() ?>
            <?php endif; ?>

            <?= Button::save()->submit() ?>

            <?php if (!$page->isNewRecord) : ?>
                <?= Button::danger(Yii::t('CustomPagesModule.views_common_edit', 'Delete'))->link(Url::toDeletePage($page), $target->container)->pjax(false)?>
            <?php endif; ?>

            <?php if (TemplateType::isType($contentType) && !$page->isNewRecord): ?>
                <?= Button::success(Yii::t('CustomPagesModule.views_common_edit', 'Inline Editor'))->link( $page->getEditUrl() )->right()->icon('fa-pencil')?>
            <?php endif; ?>
        <?php ActiveForm::end(); ?>
    </div>
</div>