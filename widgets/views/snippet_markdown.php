<?php

use humhub\modules\content\widgets\richtext\RichText;
use humhub\modules\custom_pages\widgets\SnippetContent;

/* @var $model \humhub\modules\custom_pages\models\CustomContentContainer */
/* @var $canEdit bool */

$navigation = (!$canEdit) ? [] : [
    '<a href="'.$model->getEditUrl().'"><i class="fa fa-pencil"></i>' . Yii::t('CustomPagesModule.base', 'Edit') . '</a>'
];
?>
<?= SnippetContent::widget([
    'model' => $model,
    'content' => RichText::output( $model->getPageContent()),
    'navigation' => $navigation
]); ?>