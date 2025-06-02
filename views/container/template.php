<?php
/** @var CustomPage $page **/
/** @var boolean $canEdit **/
/** @var string $mode **/
/** @var string $html **/

use humhub\modules\custom_pages\models\CustomPage;
use humhub\modules\custom_pages\modules\template\widgets\TemplatePage;
use humhub\modules\custom_pages\modules\template\widgets\TemplatePageEditButton;

$contentContainer = property_exists(Yii::$app->controller, 'contentContainer') ? Yii::$app->controller->contentContainer : null;
?>

<?php TemplatePage::begin(['page' => $page, 'canEdit' => $canEdit, 'mode' => $mode, 'contentContainer' => $contentContainer]) ?>
    <?php if($canEdit) : ?>
        <div style="margin-bottom:5px;">
            <?= TemplatePageEditButton::widget(['page' => $page, 'canEdit' => $canEdit, 'mode' => $mode]); ?>
        </div>
    <?php endif; ?>
    <?= $html; ?>
<?php TemplatePage::end() ?>
