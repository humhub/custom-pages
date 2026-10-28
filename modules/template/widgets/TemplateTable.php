<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\widgets;

use humhub\components\table\DataTable;
use humhub\modules\custom_pages\modules\template\components\TemplateTable as TemplateTableDefinition;
use humhub\widgets\DataTableWidget;
use humhub\widgets\menu\CardHeaderActions;
use Yii;

/**
 * The templates (Administration › Custom Pages › Templates) as a `<data-table>` island
 * ({@see TemplateTableDefinition}), with "Import" and "Create" ({@see TemplateCardActions}) and
 * the rows' endpoint, `GET /api/v2/custom-pages/template`.
 */
class TemplateTable extends DataTableWidget
{
    /**
     * @inheritdoc
     */
    public string $tableId = 'custom-pages-templates';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->title = $this->getLabel();
    }

    /**
     * @inheritdoc
     */
    protected function createTable(): DataTable
    {
        return Yii::createObject(TemplateTableDefinition::class);
    }

    /**
     * @inheritdoc
     */
    protected function getUrl(): string|array
    {
        return ['/custom_pages/template/api/template-table/index'];
    }

    /**
     * @inheritdoc
     */
    protected function getLabel(): string
    {
        return Yii::t('CustomPagesModule.base', 'Templates');
    }

    /**
     * @inheritdoc
     */
    protected function getCountLabels(): array
    {
        return [
            'one' => Yii::t('CustomPagesModule.template', '{count} template'),
            'other' => Yii::t('CustomPagesModule.template', '{count} templates'),
            'range' => Yii::t('CustomPagesModule.template', '{from}–{to} of {total} templates'),
        ];
    }

    /**
     * @inheritdoc
     */
    protected function getHeaderActions(): ?CardHeaderActions
    {
        return new TemplateCardActions();
    }
}
