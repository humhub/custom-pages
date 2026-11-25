<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2022 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

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
    'content' => $model->getPageContent(),
    'navigation' => $navigation
]); ?>
