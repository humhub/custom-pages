<?php
use humhub\modules\custom_pages\helpers\Url;
use humhub\modules\content\helpers\ContentContainerHelper;
use humhub\modules\custom_pages\modules\template\helpers\PagePermissionHelper;
use humhub\modules\custom_pages\modules\template\models\TemplateInstance;
use humhub\modules\custom_pages\modules\template\services\TemplateInstanceRendererService;
use humhub\widgets\bootstrap\Button;
use humhub\widgets\bootstrap\Link;

/* @var int $pageId */
/* @var TemplateInstance $templateInstance */
/* @var string $sguid */
?>

<?php if (TemplateInstanceRendererService::inEditMode()) : ?>

    <div id="editPageButton" class="btn-group">
        <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fa fa-pencil"></i>
        </button>
        <ul class="dropdown-menu">
            <li>
                <?= Link::to(
                    Yii::t('CustomPagesModule.view', 'Page configuration'),
                    Url::toEditPage($pageId, ContentContainerHelper::getCurrent()),
                )->blank()->cssClass('dropdown-item') ?>
            </li>
            <?php if (PagePermissionHelper::canTemplate()) : ?>
                <li>
                    <?= Link::to(
                        Yii::t('CustomPagesModule.view', 'Edit template'),
                        ['/custom_pages/template/admin/edit-source', 'id' => $templateInstance->template_id, 'sguid' => $sguid],
                    )->blank()->cssClass('dropdown-item') ?>
                </li>
            <?php endif; ?>
            <li>
                <?= Link::to(Yii::t('CustomPagesModule.view', 'Edit elements'))->action(
                    'ui.modal.load',
                    ['/custom_pages/template/element-content/edit-multiple', 'id' => $templateInstance->id, 'sguid' => $sguid],
                )->cssClass('dropdown-item') ?>
            </li>
            <li>
                <?= Link::to(Yii::t('CustomPagesModule.view', 'Turn edit off'), ['view', 'id' => $pageId, 'sguid' => $sguid])
                    ->cssClass('dropdown-item') ?>
            </li>
        </ul>
    </div>

<?php else: ?>
    <?= Button::primary(Yii::t('CustomPagesModule.template', 'Edit Page'))
        ->icon('pencil')
        ->link(['view', 'id' => $pageId, 'mode' => 'edit', 'sguid' => $sguid])
        ->id('editPageButton')
        ->sm() ?>
<?php endif; ?>
