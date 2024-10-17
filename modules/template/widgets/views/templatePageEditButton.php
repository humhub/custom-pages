<?php
use humhub\modules\custom_pages\helpers\Url;
use humhub\modules\content\helpers\ContentContainerHelper;

/* @var int $pageId */
?>

<?php if ($editMode) : ?>

    <div id="editPageButton" class="btn-group">
        <button type="button" class="btn btn-primary btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fa fa-pencil"></i>&nbsp;&nbsp;<span class="caret"></span>
        </button>
        <ul class="dropdown-menu">
            <li>
                <a target="_blank"  href="<?= Url::toEditPage($pageId, ContentContainerHelper::getCurrent()) ?>">
                    <?= Yii::t('CustomPagesModule.view', 'Page configuration') ?>
                </a>
            </li>
            <?php if(humhub\modules\custom_pages\modules\template\models\PagePermission::canTemplate()): ?>
                <li>
                    <a target="_blank"  href="<?= Url::to(['/custom_pages/template/layout-admin/edit-source', 'id' => $templateInstance->template_id, 'sguid' => $sguid]) ?>">
                        <?= Yii::t('CustomPagesModule.view', 'Edit template') ?>
                    </a>
                </li>
            <?php endif; ?>
            <li>
                <a data-action-click="ui.modal.load" data-action-data-type="json" data-action-url="<?= Url::to(['/custom_pages/template/owner-content/edit-multiple', 'id' => $templateInstance->id, 'sguid' => $sguid]) ?>" id="editAllElements" href="#">
                    <?= Yii::t('CustomPagesModule.view', 'Edit elements') ?>
                </a>
            </li>
            <li>
                <a href="<?= Url::to(['view', 'id' => $pageId, 'editMode' => false, 'sguid' => $sguid]); ?>">
                    <?= Yii::t('CustomPagesModule.view', 'Turn edit off') ?>
                </a>
            </li>
        </ul>
    </div>

<?php else: ?>
    <a id="editPageButton" class="btn btn-primary btn-xs" data-ui-loader style="color:var(--text-color-highlight)" href="<?= Url::to(['view', 'id' => $pageId, 'editMode' => true, 'sguid' => $sguid]); ?>">
        <i class="fa fa-pencil"></i>
        <?= Yii::t('CustomPagesModule.template', 'Edit Page') ?>
    </a>
<?php endif; ?>
