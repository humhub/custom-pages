<?php

use humhub\modules\content\widgets\richtext\RichText;
use humhub\modules\custom_pages\models\CustomPage;
use humhub\modules\custom_pages\widgets\SnippetContent;
use humhub\widgets\menu\MenuLink;

/* @var $model CustomPage */
/* @var $canEdit bool */

$navigation = (!$canEdit) ? [] : [
    new MenuLink(['label' => Yii::t('CustomPagesModule.base', 'Edit'), 'url' => $model->getEditUrl(), 'icon' => 'pencil']),
];
?>
<?= SnippetContent::widget([
    'model' => $model,
    'content' => RichText::output( $model->getPageContent()),
    'navigation' => $navigation
]); ?>
