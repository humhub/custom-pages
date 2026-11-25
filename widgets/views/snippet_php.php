<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

use humhub\modules\custom_pages\models\CustomPage;
use humhub\modules\custom_pages\widgets\SnippetContent;
use humhub\widgets\menu\MenuLink;
use yii\base\ViewNotFoundException;

/* @var $model CustomPage */
/* @var $canEdit bool */

try {
    $content = $this->renderFile($model->getPhpViewFilePath(), ['contentContainer' => $contentContainer]);
} catch (ViewNotFoundException) {
    $content = Yii::t('CustomPagesModule.view', 'View not found');
}

$navigation = (!$canEdit) ? [] : [
    new MenuLink(['label' => Yii::t('CustomPagesModule.base', 'Edit'), 'url' => $model->getEditUrl(), 'icon' => 'pencil']),
];

?>

<?=
SnippetContent::widget([
    'model' => $model,
    'content' => $content,
    'navigation' => $navigation
]);
?>
