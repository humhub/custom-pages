<?php

use humhub\libs\Html;
use humhub\modules\content\widgets\richtext\RichTextField;
use humhub\modules\custom_pages\assets\Assets;
use humhub\modules\custom_pages\helpers\Url;
use humhub\modules\custom_pages\models\CustomPage;
use humhub\modules\custom_pages\models\TemplateType;
use humhub\modules\custom_pages\widgets\PageIconSelect;
use humhub\modules\ui\form\widgets\ActiveForm;
use humhub\widgets\Button;
use humhub\widgets\Link;

Assets::register($this);

/* @var $page CustomPage */
/* @var $subNav string */
/* @var $pageType string */

$indexUrl = Url::to(['index', 'sguid' => null]);
$deleteUrl = Url::to(['delete', 'id' => $page->id, 'sguid' => null]);

$target = $page->getTargetModel();
$contentType = $page->getContentType();
?>
<div class="panel panel-default content-edit">
    <div class="panel-heading">
        <?= Yii::t('CustomPagesModule.base', '<strong>Custom</strong> Pages'); ?>
    </div>

    <?= $subNav ?>

    <div class="panel-body">
        <?= Button::back(Url::toChooseContentType($target, $pageType), Yii::t('CustomPagesModule.base', 'Back'))->sm(); ?>

        <h4><?= Yii::t('CustomPagesModule.view', 'Configuration'); ?></h4>

        <div class="help-block">
            <?= Yii::t('CustomPagesModule.view', 'Here you can configure the general settings of your {pageLabel}.', ['pageLabel' => $page->getLabel()]) ?>
        </div>

        <?php $form = ActiveForm::begin(['enableClientValidation' => false]); ?>
        <?php if ($page->isAllowedField('title')) : ?>
            <?= $form->field($page, 'title') ?>
        <?php endif; ?>

        <?= $page->getContentType()->renderFormField($form, $page); ?>

        <?= $form->beginCollapsibleFields(Yii::t('CustomPagesModule.base', 'Menu settings')); ?>

        <?php if ($page->isAllowedField('url')) : ?>
            <?= $form->field($page, 'url') ?>
            <div class="help-block">
                <?= Yii::t('CustomPagesModule.view', 'By setting an url shortcut value, you can create a better readable url for your page. If <b>URL Rewriting</b> is enabled on your site, the value \'mypage\' will result in an url \'www.example.de/p/mypage\'.') ?>
            </div>
        <?php endif; ?>

        <?php if ($page->isAllowedField('icon')) : ?>
            <?= PageIconSelect::widget(['page' => $page]) ?>
        <?php endif; ?>

        <?php if ($page->isAllowedField('sort_order')) : ?>
            <?= $form->field($page, 'sort_order')->textInput(); ?>
        <?php endif; ?>

        <?php if ($page->isAllowedField('cssClass')) : ?>
            <?= $form->field($page, 'cssClass'); ?>
        <?php endif; ?>

        <?php if ($page->isAllowedField('in_new_window')) : ?>
            <?php if ($page->hasAttribute('in_new_window')) : ?>
                <?= $form->field($page, 'in_new_window')->checkbox() ?>
            <?php endif; ?>
        <?php endif; ?>

        <?= $form->endCollapsibleFields(); ?>

        <?php if (!$page->isSnippet()) : ?>
            <div class="alert alert-info infoAdminOnly"
                 <?php if ($page->visibility != CustomPage::VISIBILITY_ADMIN_ONLY): ?>style="display:none"<?php endif; ?>>
                <?= Yii::t('CustomPagesModule.view', '<strong>Info: </strong> Pages marked as "Admin Only" are not shown in the stream!'); ?>
            </div>
        <?php endif; ?>

        <?php if ($page->isAllowedField('abstract')) : ?>
            <?= $form->beginCollapsibleFields(Yii::t('CustomPagesModule.base', 'Stream options')); ?>
            <?= $form->field($page, 'abstract')->widget(RichTextField::class); ?>
            <div class="help-block">
                <?= Yii::t('CustomPagesModule.view',
                    'The abstract will be used as stream entry content to promote the actual page. 
                        If no abstract is given or the page is only visible for admins, no stream entry will be created.') ?>
            </div>
            <?= $form->endCollapsibleFields(); ?>
        <?php endif; ?>

        <?= $form->field($page, 'visibility')->radioList($page->getVisibilitySelection()) ?>

        <?= Button::save()->submit() ?>

        <?php if (!$page->isNewRecord) : ?>
            <?= Link::danger(Yii::t('CustomPagesModule.view', 'Delete'))->post(Url::toDeletePage($page, $target->container))->pjax(false)->confirm() ?>
        <?php endif; ?>

        <?php if (TemplateType::isType($contentType) && !$page->isNewRecord): ?>
            <?= Button::success(Yii::t('CustomPagesModule.view', 'Inline Editor'))->link(Url::toInlineEdit($page, $target->container))->right()->icon('fa-pencil') ?>
        <?php endif; ?>

        <script <?= Html::nonce() ?>>
            $(document).one('humhub:ready', function () {
                    $('input[type="radio"][name="CustomPage[visibility]"]').click(function () {
                        $('.infoAdminOnly').toggle($(this).val() == <?= CustomPage::VISIBILITY_ADMIN_ONLY ?>);
                    });
                }
            );
        </script>
        <?php ActiveForm::end(); ?>
    </div>
</div>
