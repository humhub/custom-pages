<?php
/** @var CustomPage $page **/
/** @var bool $canEdit **/
/** @var string $html **/

use humhub\modules\custom_pages\models\CustomPage;
use humhub\modules\custom_pages\modules\template\widgets\TemplatePage;
use humhub\modules\custom_pages\modules\template\widgets\TemplatePageEditButton;
?>

<?php TemplatePage::begin(['page' => $page]) ?>
    <?php if ($canEdit) : ?>
        <div style="margin-bottom:5px;">
            <?= TemplatePageEditButton::widget(['page' => $page, 'canEdit' => $canEdit]) ?>
        </div>
    <?php endif; ?>
    <?= $html; ?>
<?php TemplatePage::end() ?>
