<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\controllers\api;

use humhub\components\api\DataTableController;
use humhub\components\table\DataTable;
use humhub\modules\custom_pages\modules\template\components\TemplateTable;
use Yii;

/**
 * The rows of the template table: `GET custom-pages/template` answers the {@see TemplateTable} rows of the templates of
 * `TemplateTableList` (its filters and sorts as query parameters) in the paginated envelope, for
 * those who may manage the modules.
 */
class TemplateTableController extends DataTableController
{
    /**
     * @inheritdoc
     */
    protected function createTable(): DataTable
    {
        return Yii::createObject(TemplateTable::class);
    }
}
