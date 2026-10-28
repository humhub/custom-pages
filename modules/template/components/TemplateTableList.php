<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\components;

use humhub\components\listing\FilterableList;
use humhub\components\listing\filters\EnumFilter;
use humhub\components\listing\filters\SearchFilter;
use humhub\components\listing\ListBuilder;
use humhub\components\listing\ListContext;
use humhub\components\listing\QueryListBuilder;
use humhub\modules\custom_pages\modules\template\models\Template;
use Yii;

/**
 * The templates of the template table ({@see TemplateTable}), narrowed by a search (`q`: id or
 * name) and `type`, in the order of a column (`sort`: `name`, `type`, each also descending with
 * a leading `-`; without, by name).
 */
class TemplateTableList extends FilterableList
{
    /**
     * The sortable columns by sort key.
     */
    private const SORT_COLUMNS = [
        'name' => ['custom_pages_template.name'],
        'type' => ['custom_pages_template.type'],
    ];

    /**
     * @inheritdoc
     */
    protected function filters(): array
    {
        return [
            new SearchFilter(
                'q',
                apply: static fn(QueryListBuilder $list, string $keywords) => $list->query()->andWhere([
                    'OR',
                    ['custom_pages_template.id' => $keywords],
                    ['like', 'custom_pages_template.name', $keywords],
                ]),
                definition: [
                    'label' => Yii::t('CustomPagesModule.template', 'Search'),
                    'placeholder' => Yii::t('CustomPagesModule.template', 'Search by template ID or name'),
                    'sortOrder' => 100,
                ],
            ),
            new EnumFilter(
                'type',
                values: Template::getTypeOptions(),
                apply: static fn(QueryListBuilder $list, string $type) => $list->query()
                    ->andWhere(['custom_pages_template.type' => $type]),
                definition: [
                    'label' => Yii::t('CustomPagesModule.template', 'Type'),
                    'sortOrder' => 200,
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
        return new QueryListBuilder(Template::find());
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
            $builder->query()->orderBy(['custom_pages_template.name' => SORT_ASC, 'custom_pages_template.id' => SORT_ASC]);
            return;
        }

        $builder->sortBy($columns, str_starts_with((string)$sort, '-'), 'custom_pages_template.id');
    }
}
