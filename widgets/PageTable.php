<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\widgets;

use humhub\components\table\DataTable;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\custom_pages\components\PageTable as PageTableDefinition;
use humhub\modules\custom_pages\helpers\PageType;
use humhub\widgets\DataTableWidget;
use humhub\widgets\menu\CardHeaderActions;
use Yii;

/**
 * The pages or snippets to manage as a `<data-table>` island ({@see PageTableDefinition}), with
 * "+" to add one ({@see PageCardActions}) and the rows' endpoint,
 * `GET /api/v2/custom-pages/page|snippet` or `/space/<id>/custom-pages/page|snippet`.
 */
class PageTable extends DataTableWidget
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
    public function init()
    {
        parent::init();
        $this->tableId = 'custom-pages-' . $this->pageType . ($this->container === null ? 's' : 's-container');
        $this->title = $this->getLabel();
    }

    /**
     * @inheritdoc
     */
    protected function createTable(): DataTable
    {
        return Yii::createObject([
            'class' => PageTableDefinition::class,
            'pageType' => $this->pageType,
            'container' => $this->container,
        ]);
    }

    /**
     * @inheritdoc
     */
    protected function getUrl(): string|array
    {
        $url = ['/custom_pages/api/page-table/index', 'pageType' => $this->pageType];
        if ($this->container !== null) {
            $url['spaceId'] = $this->container->id;
        }

        return $url;
    }

    /**
     * @inheritdoc
     */
    protected function getLabel(): string
    {
        return $this->pageType === PageType::Snippet
            ? Yii::t('CustomPagesModule.base', 'Snippets')
            : Yii::t('CustomPagesModule.base', 'Pages');
    }

    /**
     * @inheritdoc
     */
    protected function getCountLabels(): array
    {
        return $this->pageType === PageType::Snippet
            ? [
                'one' => Yii::t('CustomPagesModule.base', '{count} snippet'),
                'other' => Yii::t('CustomPagesModule.base', '{count} snippets'),
                'range' => Yii::t('CustomPagesModule.base', '{from}–{to} of {total} snippets'),
            ]
            : [
                'one' => Yii::t('CustomPagesModule.base', '{count} page'),
                'other' => Yii::t('CustomPagesModule.base', '{count} pages'),
                'range' => Yii::t('CustomPagesModule.base', '{from}–{to} of {total} pages'),
            ];
    }

    /**
     * @inheritdoc
     */
    protected function getHeaderActions(): ?CardHeaderActions
    {
        return new PageCardActions(['pageType' => $this->pageType, 'contentContainer' => $this->container]);
    }
}
