<?php

use humhub\modules\custom_pages\helpers\PageType;
use humhub\modules\custom_pages\models\CustomPage;
use humhub\modules\custom_pages\modules\template\widgets\TemplatePage;
use humhub\modules\custom_pages\modules\template\widgets\TemplatePageEditButton;
use humhub\widgets\FooterMenu;

/* @var CustomPage $page */
/* @var string $html */
?>
<?php TemplatePage::begin(['page' => $page]) ?>
<div class="row">
    <div class="col-md-12">
        <?= TemplatePageEditButton::widget(['page' => $page]) ?>
        <?= $html ?>
    </div>
</div>
<?php TemplatePage::end() ?>

<?php if (!$page->hasTarget(PageType::TARGET_ACCOUNT_MENU)): ?>
    <?= FooterMenu::widget() ?>
<?php endif; ?>
