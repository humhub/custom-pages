<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

use humhub\modules\custom_pages\widgets\SnippetContent;
use yii\base\ViewNotFoundException;
use yii\helpers\Url;

try {
    $content = $this->renderFile($model->getPhpViewFilePath(), ['contentContainer' => $contentContainer]);
} catch (ViewNotFoundException $vnfe) {
    $content = Yii::t('CustomPagesModule.view_php', 'View not found');
}

if ($contentContainer != null) {
    $editUrl = $contentContainer->createUrl('/custom_pages/container-snippet/edit', ['id' => $model->id]);
} else {
    $editUrl = Url::to(['/custom_pages/snippet/edit', 'id' => $model->id]);
}

$navigation = (!$canEdit) ? [] : [
    '<a href="' . $editUrl . '" class="panel-collapse"><i class="fa fa-pencil"></i>' . Yii::t('CustomPagesModule.base', 'Edit') . '</a>'
];

?>

<?=
SnippetContent::widget([
    'model' => $model,
    'content' => $content,
    'navigation' => $navigation
]);
?>



