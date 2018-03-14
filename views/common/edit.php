<?php

use humhub\modules\custom_pages\widgets\PageIconSelect;
use humhub\widgets\MarkdownEditor;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use humhub\modules\custom_pages\models\Page;
use humhub\modules\custom_pages\models\ContainerPage;
use humhub\modules\custom_pages\components\Container;
use humhub\modules\custom_pages\models\Snippet;
use humhub\modules\custom_pages\models\ContainerSnippet;

/** @var  $page mixed */
/** @var  $subNav string */

// Todo: use new ContentContainerHelper class prior to 1.3
$sguid = Yii::$app->request->get('sguid') ? Yii::$app->request->get('sguid') : Yii::$app->request->get('cguid');

$indexUrl = Url::to(['index', 'sguid' => $sguid]);
$deleteUrl = Url::to(['delete', 'id' => $page->id, 'sguid' => $sguid]);

?>
<div class="panel panel-default">
    <div class="panel-heading">
        <?= Yii::t('CustomPagesModule.base', '<strong>Custom</strong> Pages'); ?>
    </div>

    <?= $subNav ?>

    <div class="panel-body">

        <?= Html::a('<i class="fa fa-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;' . Yii::t('CustomPagesModule.base', 'Back to overview'), $indexUrl, ['data-ui-loader' => '', 'class' => 'btn btn-default pull-right']); ?>

        <h4><?= Yii::t('CustomPagesModule.views_common_edit', 'Configuration'); ?></h4>

        <div class="help-block">
            <?= Yii::t('CustomPagesModule.views_common_edit', 'Here you can configure the general settings of your {label}.', ['label' => $page->getLabel()]) ?>
        </div>

        <?php $form = ActiveForm::begin(); ?>

        <div class="form-group"> 
            <?= Html::textInput('type', Container::getLabel($page->type), ['class' => 'form-control', 'disabled' => '1']); ?>
        </div>

        <?= $form->field($page, 'title') ?>
        
        <?= PageIconSelect::widget(['page' => $page]) ?>

        <?php if ($page->isType(Container::TYPE_LINK) || $page->isType(Container::TYPE_IFRAME)): ?>
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

        <?php if ($page->isType(Container::TYPE_HTML)): ?>
            <?= $form->field($page, $page->getPageContentProperty())->textarea(['id' => 'html_content', 'class' => 'form-control', 'rows' => '15']); ?>
        <?php elseif ($page->isType(Container::TYPE_TEMPLATE)): ?>
            <?= $form->field($page, 'templateId')->dropDownList($page->getAllowedTemplateSelection(), ['value' => $page->getTemplateId(), 'disabled' => !$page->isNewRecord]) ?>
        <?php elseif ($page->isType(Container::TYPE_MARKDOWN)): ?>
            <?= $form->field($page, $page->getPageContentProperty())->textarea(['id' => 'markdownField', 'class' => 'form-control', 'rows' => '15']); ?>
            <?= MarkdownEditor::widget(['fieldId' => 'markdownField']); ?>
        <?php elseif ($page->isType(Container::TYPE_PHP)): ?>
            <?=  $form->field($page, $page->getPageContentProperty())->dropDownList($page->getAllowedPhpViewFileSelection()) ?>
        <?php endif; ?>

        <?php if ($page instanceof Page) : ?> 
            <?= $form->field($page, 'navigation_class')->dropDownList(Page::getNavigationClasses()); ?>
        <?php endif; ?>

        <?php if ($page instanceof Snippet) : ?> 
            <?= $form->field($page, 'sidebar')->dropDownList(Snippet::getSidebarSelection()); ?>
        <?php endif; ?>

        <?= $form->field($page, 'sort_order')->textInput(); ?>

        <?php if ($page->hasAttribute('cssClass') && !$page->isType(Container::TYPE_LINK)) : ?>
            <?= $form->field($page, 'cssClass'); ?>
        <?php endif; ?>

        <?php if ($page->hasAttribute('admin_only')) : ?>
            <?= $form->field($page, 'admin_only')->checkbox() ?>
        <?php endif; ?>

        <?php if ($page->hasAttribute('in_new_window')) : ?> 
            <?= $form->field($page, 'in_new_window')->checkbox() ?>
        <?php endif; ?>

        <?= Html::submitButton(Yii::t('CustomPagesModule.views_common_edit', 'Save'), ['class' => 'btn btn-primary', 'data-ui-loader' => '']); ?>

        <?php if (!$page->isNewRecord) : ?>
            <?= Html::a(Yii::t('CustomPagesModule.views_common_edit', 'Delete'), $deleteUrl, ['class' => 'btn btn-danger', 'data-ui-loader' => '', 'data-pjax-prevent' => '']); ?>
        <?php endif; ?>

        <?php if ($page->isType(Container::TYPE_TEMPLATE) && !$page->isNewRecord): ?>
            <?php if ($page instanceof Snippet) : ?>
                <?php $url = Url::to(['/custom_pages/snippet/edit-snippet', 'id' => $page->id]); ?>
            <?php elseif ($page instanceof ContainerSnippet) : ?>
                <?php $url = Url::to(['/custom_pages/container-snippet/edit-snippet', 'id' => $page->id, 'sguid' => $sguid]); ?>
            <?php elseif ($page instanceof ContainerPage) : ?>
                <?php $url = Url::to(['/custom_pages/container/view', 'id' => $page->id, 'editMode' => 1, 'sguid' => $sguid]); ?>
            <?php else : ?>
                <?php $url = Url::to(['/custom_pages/view/view', 'id' => $page->id, 'editMode' => 1]); ?>
            <?php endif; ?>
            <?= Html::a('<i class="fa fa-pencil"></i> ' . Yii::t('CustomPagesModule.views_common_edit', 'Inline Editor'), $url, ['class' => 'btn btn-success pull-right', 'data-ui-loader' => '']);
            ?>
        <?php endif; ?>
        <?php ActiveForm::end(); ?>
    </div>
</div>