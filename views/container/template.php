<?php
/** @var \humhub\modules\custom_pages\models\CustomContentContainer $page **/
/** @var boolean $canEdit **/
/** @var boolean $editMode **/
/** @var string $html **/

use humhub\modules\custom_pages\modules\template\widgets\TemplatePage;
?>

<?php TemplatePage::begin(['page' => $page, 'canEdit' => $canEdit, 'editMode' => $editMode]) ?>
    <?php if($canEdit) : ?>
        <div style="margin-bottom:5px;">
            <?= \humhub\modules\custom_pages\modules\template\widgets\TemplatePageEditButton::widget(['page' => $page, 'canEdit' => $canEdit, 'editMode' => $editMode]); ?>
        </div>
    <?php endif; ?>
    <?= $html; ?>
<?php TemplatePage::end() ?>
