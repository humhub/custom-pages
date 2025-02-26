<?php
use humhub\modules\custom_pages\helpers\Url;
use humhub\modules\content\helpers\ContentContainerHelper;
use humhub\modules\custom_pages\modules\template\helpers\PagePermissionHelper;
use humhub\modules\custom_pages\modules\template\models\TemplateInstance;
use humhub\widgets\Button;
use humhub\widgets\Link;

/* @var int $pageId */
/* @var string $mode */
/* @var TemplateInstance $templateInstance */
/* @var string $sguid */
?>

<?php if ($mode === 'edit' || $mode === 'structure') : ?>

    <div id="editPageButton" class="btn-group">
        <button type="button" class="btn btn-primary btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fa fa-pencil"></i>&nbsp;&nbsp;<span class="caret"></span>
        </button>
        <ul class="dropdown-menu">
            <li>
                <?= Link::to(
                        Yii::t('CustomPagesModule.view', 'Page configuration'),
                        Url::toEditPage($pageId, ContentContainerHelper::getCurrent()),
                    )->blank() ?>
            </li>
            <?php if (PagePermissionHelper::canTemplate()) : ?>
                <li>
                    <?= Link::to(
                            Yii::t('CustomPagesModule.view', 'Edit template'),
                            ['/custom_pages/template/layout-admin/edit-source', 'id' => $templateInstance->template_id, 'sguid' => $sguid],
                        )->blank() ?>
                </li>
            <?php endif; ?>
            <li>
                <?= Link::to(
                    $mode === 'edit'
                        ? Yii::t('CustomPagesModule.view', 'Structure Overview')
                        : Yii::t('CustomPagesModule.view', 'Edit mode'),
                    ['view', 'id' => $pageId, 'mode' => $mode === 'edit' ? 'structure' : 'edit', 'sguid' => $sguid],
                ) ?>
            </li>
            <li>
                <?= Link::to(Yii::t('CustomPagesModule.view', 'Edit elements'))->action(
                    'ui.modal.load',
                    ['/custom_pages/template/element-content/edit-multiple', 'id' => $templateInstance->id, 'sguid' => $sguid],
                ) ?>
            </li>
            <li>
                <?= Link::to(
                    $mode === 'edit'
                        ? Yii::t('CustomPagesModule.view', 'Turn edit off')
                        : Yii::t('CustomPagesModule.view', 'Turn structure off'),
                    ['view', 'id' => $pageId, 'sguid' => $sguid],
                ) ?>
            </li>
        </ul>
    </div>

<?php else: ?>
    <?= Button::primary(Yii::t('CustomPagesModule.template', 'Edit Page'))
        ->icon('pencil')
        ->link(['view', 'id' => $pageId, 'mode' => 'edit', 'sguid' => $sguid])
        ->id('editPageButton')
        ->xs() ?>
<?php endif; ?>
