<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\components;

use humhub\components\listing\FilterableList;
use humhub\components\table\ColumnDefinition;
use humhub\components\table\DataTable;
use humhub\components\table\RowAction;
use humhub\modules\admin\permissions\ManageModules;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\custom_pages\helpers\PageType;
use humhub\modules\custom_pages\helpers\Url;
use humhub\modules\custom_pages\models\CustomPage;
use humhub\modules\custom_pages\permissions\ManagePages;
use humhub\modules\custom_pages\types\TemplateType;
use humhub\modules\space\models\Space;
use Yii;

/**
 * The pages or snippets to manage (Administration › Custom Pages, a space's Custom Pages;
 * `custom_pages\widgets\PageTable`, rows from `GET /api/v2/custom-pages/page|snippet` or
 * `/space/<id>/custom-pages/page|snippet`): title, target, content type and sort order, with
 * the actions edit, inline editor, view, copy and delete.
 *
 * Created through the DI container, so a module may replace it or change columns and actions on
 * {@see self::EVENT_INIT}.
 */
class PageTable extends DataTable
{
    /**
     * @var string the page type, {@see PageType::Page} or {@see PageType::Snippet}
     */
    public string $pageType = PageType::Page;

    /**
     * @var ContentContainerActiveRecord|null the space whose pages are listed; the global pages
     * without
     */
    public ?ContentContainerActiveRecord $container = null;

    /**
     * @inheritdoc
     */
    protected function createList(): FilterableList
    {
        return new PageTableList(['pageType' => $this->pageType, 'container' => $this->container]);
    }

    /**
     * @inheritdoc
     */
    protected function columns(): array
    {
        return [
            new ColumnDefinition('title', ColumnDefinition::TYPE_MAIN, Yii::t('CustomPagesModule.base', 'Title'), sort: 'title', sortOrder: 100),
            new ColumnDefinition('target', ColumnDefinition::TYPE_CHIP, Yii::t('CustomPagesModule.base', 'Target'), priority: 2, filter: 'target', sortOrder: 200),
            new ColumnDefinition('type', ColumnDefinition::TYPE_CHIP, Yii::t('CustomPagesModule.base', 'Type'), priority: 3, filter: 'type', sortOrder: 300),
            new ColumnDefinition('sortOrder', ColumnDefinition::TYPE_NUMBER, Yii::t('CustomPagesModule.base', 'Sort Order'), priority: 1, sort: 'sortOrder', sortOrder: 400, narrow: true),
        ];
    }

    /**
     * @inheritdoc
     */
    protected function actions(): array
    {
        $canEdit = static fn(CustomPage $page) => $page->canEdit() && $page->canEditType((int)$page->type);

        return [
            new RowAction(
                'edit',
                Yii::t('base', 'Edit'),
                RowAction::KIND_LINK,
                static fn(CustomPage $page) => $page->getEditUrl(),
                icon: 'pencil',
                visible: $canEdit,
                sortOrder: 100,
            ),
            new RowAction(
                'inline-edit',
                Yii::t('CustomPagesModule.view', 'Inline Editor'),
                RowAction::KIND_LINK,
                static fn(CustomPage $page) => Url::toInlineEdit($page, $page->content->container),
                icon: 'edit',
                visible: static fn(CustomPage $page) => TemplateType::isType($page->type) && $canEdit($page),
                sortOrder: 200,
            ),
            new RowAction(
                'view',
                Yii::t('CustomPagesModule.base', 'View'),
                RowAction::KIND_LINK,
                static fn(CustomPage $page) => $page->getUrl(),
                icon: 'eye',
                visible: static fn(CustomPage $page) => $page->getTargetModel() !== null && !$page->isSnippet(),
                sortOrder: 300,
            ),
            new RowAction(
                'copy',
                Yii::t('base', 'Copy'),
                RowAction::KIND_MODAL,
                static fn(CustomPage $page) => Url::toCopyPage($page),
                icon: 'copy',
                visible: $canEdit,
                sortOrder: 400,
            ),
            new RowAction(
                'delete',
                Yii::t('base', 'Delete'),
                RowAction::KIND_POST,
                static fn(CustomPage $page) => Url::toDeletePage($page, $page->content->container),
                icon: 'trash',
                confirm: Yii::t('CustomPagesModule.base', 'Do you really want to delete this {pageLabel}?', [
                    'pageLabel' => $this->pageType === PageType::Snippet
                        ? Yii::t('CustomPagesModule.model', 'snippet')
                        : Yii::t('CustomPagesModule.model', 'page'),
                ]),
                destructive: true,
                visible: static fn(CustomPage $page) => $page->content->canEdit(),
                sortOrder: 100,
            ),
        ];
    }

    /**
     * @inheritdoc
     */
    protected function cells($model): array
    {
        /** @var CustomPage $model */
        $target = $model->getTargetModel();
        $contentType = $model->getContentType();

        return [
            'title' => [
                'text' => $model->getTitle(),
                'subtitle' => $model->hasAttribute('url') && $model->url ? $model->url : null,
                'url' => $model->canEdit() && $model->canEditType((int)$model->type) ? $model->getEditUrl() : null,
            ],
            'target' => $target === null ? [] : [['text' => $target->name, 'value' => $target->id]],
            'type' => $contentType === null ? [] : [['text' => $contentType->getLabel(), 'value' => (string)$contentType->getId()]],
            'sortOrder' => ['text' => (string)(int)$model->sort_order],
        ];
    }

    /**
     * Whether the viewer may manage the pages: the global ones who may manage the modules or the
     * pages, a space's its administrators (as the pages' overview).
     */
    public function canAccess(): bool
    {
        if ($this->container === null) {
            return Yii::$app->user->can([ManageModules::class, ManagePages::class]);
        }

        return $this->container instanceof Space && $this->container->isAdmin();
    }
}
