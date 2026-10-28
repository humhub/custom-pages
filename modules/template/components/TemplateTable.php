<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\components;

use humhub\components\listing\FilterableList;
use humhub\components\table\ColumnDefinition;
use humhub\components\table\DataTable;
use humhub\components\table\RowAction;
use humhub\modules\admin\permissions\ManageModules;
use humhub\modules\custom_pages\modules\template\models\Template;
use Yii;
use yii\helpers\Url;

/**
 * The templates (Administration › Custom Pages › Templates; `template\widgets\TemplateTable`,
 * rows from `GET /api/v2/custom-pages/template`): name with description, type, usage and
 * whether it is a default template, with the actions edit (or view), usage, copy, export and
 * delete.
 *
 * Created through the DI container, so a module may replace it or change columns and actions on
 * {@see self::EVENT_INIT}.
 */
class TemplateTable extends DataTable
{
    /**
     * @inheritdoc
     */
    protected function createList(): FilterableList
    {
        return new TemplateTableList();
    }

    /**
     * @inheritdoc
     */
    protected function columns(): array
    {
        return [
            new ColumnDefinition('name', ColumnDefinition::TYPE_MAIN, Yii::t('CustomPagesModule.template', 'Name'), sort: 'name', sortOrder: 100),
            new ColumnDefinition('type', ColumnDefinition::TYPE_CHIP, Yii::t('CustomPagesModule.template', 'Type'), priority: 1, sort: 'type', filter: 'type', sortOrder: 200),
            new ColumnDefinition('usage', ColumnDefinition::TYPE_NUMBER, Yii::t('CustomPagesModule.template', 'Usage'), priority: 2, sortOrder: 300, narrow: true),
            new ColumnDefinition('id', ColumnDefinition::TYPE_NUMBER, 'ID', priority: 3, sortOrder: 400, visible: false, narrow: true),
        ];
    }

    /**
     * @inheritdoc
     */
    protected function actions(): array
    {
        return [
            new RowAction(
                'edit',
                Yii::t('CustomPagesModule.template', 'Edit'),
                RowAction::KIND_LINK,
                static fn(Template $template) => ['/custom_pages/template/admin/edit-source', 'id' => $template->id],
                icon: 'pencil',
                visible: static fn(Template $template) => $template->canEdit(),
                sortOrder: 100,
            ),
            new RowAction(
                'view',
                Yii::t('CustomPagesModule.template', 'View'),
                RowAction::KIND_LINK,
                static fn(Template $template) => ['/custom_pages/template/admin/edit-source', 'id' => $template->id],
                icon: 'eye',
                visible: static fn(Template $template) => !$template->canEdit(),
                sortOrder: 100,
            ),
            new RowAction(
                'usage',
                Yii::t('CustomPagesModule.template', 'Usage'),
                RowAction::KIND_MODAL,
                static fn(Template $template) => ['/custom_pages/template/admin/edit-usage-modal', 'id' => $template->id],
                icon: 'list',
                sortOrder: 200,
            ),
            new RowAction(
                'copy',
                Yii::t('CustomPagesModule.template', 'Copy'),
                RowAction::KIND_LINK,
                static fn(Template $template) => ['/custom_pages/template/admin/copy', 'id' => $template->id],
                icon: 'copy',
                sortOrder: 300,
            ),
            new RowAction(
                'export',
                Yii::t('CustomPagesModule.template', 'Export'),
                RowAction::KIND_LINK,
                static fn(Template $template) => ['/custom_pages/template/admin/export-source', 'id' => $template->id],
                icon: 'upload',
                sortOrder: 400,
            ),
            new RowAction(
                'delete',
                Yii::t('CustomPagesModule.template', 'Delete'),
                RowAction::KIND_POST,
                static fn(Template $template) => ['/custom_pages/template/admin/delete-template', 'id' => $template->id],
                icon: 'trash',
                confirm: Yii::t('CustomPagesModule.template', 'Do you really want to delete this template?'),
                destructive: true,
                visible: static fn(Template $template) => $template->canDelete(),
                sortOrder: 100,
            ),
        ];
    }

    /**
     * @inheritdoc
     */
    protected function cells($model): array
    {
        /** @var Template $model */
        $type = [['text' => Template::getTypeTitle($model->type), 'value' => $model->type]];
        if ($model->is_default) {
            $type[] = ['text' => Yii::t('CustomPagesModule.template', 'Default')];
        }

        return [
            'name' => [
                'text' => $model->name,
                'subtitle' => $model->description ?: null,
                'url' => Url::to(['/custom_pages/template/admin/edit-source', 'id' => $model->id]),
            ],
            'type' => $type,
            'usage' => ['text' => (string)$model->getLinkedRecordsQuery()->count()],
            'id' => ['text' => (string)$model->id],
        ];
    }

    /**
     * Whether the viewer may see the table: those who may manage the modules, as the template
     * administration.
     */
    public function canAccess(): bool
    {
        return Yii::$app->user->can(ManageModules::class);
    }
}
