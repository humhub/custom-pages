<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

use humhub\modules\custom_pages\models\CustomPage;
use humhub\modules\custom_pages\widgets\SnippetContent;
use yii\base\ViewNotFoundException;

/* @var $model CustomPage */
/* @var $canEdit bool */

try {
    $content = $this->renderFile($model->getPhpViewFilePath(), ['contentContainer' => $contentContainer]);
} catch (ViewNotFoundException) {
    $content = Yii::t('CustomPagesModule.view', 'View not found');
}

$navigation = (!$canEdit) ? [] : [
    '<a href="' . $model->getEditUrl() . '"><i class="fa fa-pencil"></i>' . Yii::t('CustomPagesModule.base', 'Edit') . '</a>'
];

?>

<?=
SnippetContent::widget([
    'model' => $model,
    'content' => $content,
    'navigation' => $navigation
]);
?>
