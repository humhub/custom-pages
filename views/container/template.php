<?php

use humhub\modules\custom_pages\models\CustomPage;
use humhub\modules\custom_pages\modules\template\widgets\TemplatePage;
use humhub\modules\custom_pages\modules\template\widgets\TemplatePageEditButton;

/* @var CustomPage $page */
/* @var string $html */
?>
<?php TemplatePage::begin(['page' => $page]) ?>
    <?= TemplatePageEditButton::widget(['page' => $page]) ?>
    <?= $html ?>
<?php TemplatePage::end() ?>
