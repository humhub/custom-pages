<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\components;

use humhub\components\listing\FilterableList;
use humhub\components\listing\filters\EnumFilter;
use humhub\components\listing\filters\SearchFilter;
use humhub\components\listing\ListBuilder;
use humhub\components\listing\ListContext;
use humhub\components\listing\QueryListBuilder;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\custom_pages\helpers\PageType;
use humhub\modules\custom_pages\interfaces\CustomPagesService;
use humhub\modules\custom_pages\models\CustomPage;
use humhub\modules\custom_pages\types\ContentType;
use Yii;

/**
 * The pages or snippets of a page table ({@see PageTable}): the global ones or those of a space,
 * narrowed by a search (`q`: title), `target` and `type` (the content type), in the order of a
 * column (`sort`: `title`, `sortOrder`, each also descending with a leading `-`; without, by
 * sort order, then by title).
 */
class PageTableList extends FilterableList
{
    /**
     * The sortable columns by sort key.
     */
    private const SORT_COLUMNS = [
        'title' => ['custom_pages_page.title'],
        'sortOrder' => ['custom_pages_page.sort_order'],
    ];

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
    protected function filters(): array
    {
        $targets = array_column(CustomPagesService::instance()->getTargets($this->pageType, $this->container), 'name', 'id');

        $types = [];
        foreach (ContentType::getContentTypes() as $contentType) {
            $types[(string)$contentType->getId()] = $contentType->getLabel();
        }

        return [
            new SearchFilter(
                'q',
                columns: ['custom_pages_page.title'],
                definition: [
                    'label' => Yii::t('CustomPagesModule.base', 'Search'),
                    'placeholder' => Yii::t('CustomPagesModule.base', 'Title'),
                    'sortOrder' => 100,
                ],
            ),
            new EnumFilter(
                'target',
                values: $targets,
                apply: static fn(QueryListBuilder $list, string $target) => $list->query()
                    ->andWhere(['custom_pages_page.target' => $target]),
                definition: count($targets) < 2 ? null : [
                    'label' => Yii::t('CustomPagesModule.base', 'Target'),
                    'sortOrder' => 200,
                ],
            ),
            new EnumFilter(
                'type',
                values: $types,
                apply: static fn(QueryListBuilder $list, string $type) => $list->query()
                    ->andWhere(['custom_pages_page.type' => (int)$type]),
                definition: [
                    'label' => Yii::t('CustomPagesModule.base', 'Type'),
                    'sortOrder' => 300,
                ],
            ),
        ];
    }

    /**
     * @inheritdoc
     */
    protected function sorts(): array
    {
        // Accepted, not offered in a sort select: the table's column headers sort.
        $sorts = [];
        foreach (array_keys(self::SORT_COLUMNS) as $key) {
            $sorts[$key] = null;
            $sorts['-' . $key] = null;
        }

        return $sorts;
    }

    /**
     * @inheritdoc
     */
    protected function createBuilder(ListContext $context): ListBuilder
    {
        return new QueryListBuilder(CustomPagesService::instance()->findByPageType($this->pageType, $this->container));
    }

    /**
     * @inheritdoc
     */
    protected function finalize(ListBuilder $builder, array $values, ListContext $context): void
    {
        /** @var QueryListBuilder $builder */
        $sort = $this->value($values, self::SORT_PARAM);
        if ($sort !== null && $this->applySortCallback($builder, $sort, $context)) {
            return;
        }

        $columns = self::SORT_COLUMNS[ltrim((string)$sort, '-')] ?? null;
        if ($columns === null) {
            $builder->query()->orderBy([
                'custom_pages_page.sort_order' => SORT_ASC,
                'custom_pages_page.title' => SORT_ASC,
                'custom_pages_page.id' => SORT_ASC,
            ]);
            return;
        }

        $builder->sortBy($columns, str_starts_with((string)$sort, '-'), 'custom_pages_page.id');
    }
}
