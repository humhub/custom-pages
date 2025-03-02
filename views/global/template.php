<?php
/* @var CustomPage $page */
/* @var boolean $canEdit */
/* @var string $mode */
/* @var string $html */

use humhub\modules\custom_pages\models\CustomPage;
use humhub\modules\custom_pages\modules\template\widgets\TemplatePage;
use humhub\modules\custom_pages\modules\template\widgets\TemplatePageEditButton;
?>

<?php TemplatePage::begin(['page' => $page, 'canEdit' => $canEdit, 'mode' => $mode]) ?>
<div class="row">
    <div class="col-md-12">
        <?php if ($canEdit) : ?>
            <div style="margin-bottom:5px;">
                <?= TemplatePageEditButton::widget(['page' => $page, 'canEdit' => $canEdit, 'mode' => $mode]); ?>
            </div>
        <?php endif; ?>
        <?= $html; ?>
    </div>
</div>
<?php TemplatePage::end() ?>
