<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2022 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\modules\custom_pages\models\Page;
use humhub\modules\custom_pages\widgets\SnippetContent;

/* @var $model Page */
/* @var $canEdit bool */

$navigation = (!$canEdit) ? [] : [
    '<a href="'.$model->getEditUrl().'"><i class="fa fa-pencil"></i>' . Yii::t('CustomPagesModule.base', 'Edit') . '</a>'
];
?>
<?= SnippetContent::widget([
    'model' => $model,
    'content' => $model->getPageContent(),
    'navigation' => $navigation
]); ?>
