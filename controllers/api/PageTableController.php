<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\controllers\api;

use humhub\components\api\DataTableController;
use humhub\components\table\DataTable;
use humhub\modules\custom_pages\components\PageTable;
use humhub\modules\custom_pages\helpers\PageType;
use humhub\modules\space\models\Space;
use Yii;
use yii\web\NotFoundHttpException;

/**
 * The pages or snippets to manage as rows of a page table ({@see PageTable}):
 * `GET custom-pages/page|snippet` the global ones,
 * `GET space/<id>/custom-pages/page|snippet` a space's — one page of them (the list's filters
 * and sorts as query parameters) in the paginated envelope.
 *
 * For those who may manage them ({@see PageTable::canAccess()}). Pages are edited, copied and
 * deleted through the pages' own routes, which the rows' actions name.
 */
class PageTableController extends DataTableController
{
    /**
     * @inheritdoc
     */
    protected function createTable(): DataTable
    {
        $request = Yii::$app->request;

        $pageType = $request->get('pageType');
        if (!in_array($pageType, [PageType::Page, PageType::Snippet], true)) {
            throw new NotFoundHttpException();
        }

        $container = null;
        if ($request->get('spaceId') !== null) {
            $container = Space::findOne(['id' => (int)$request->get('spaceId')]);
            if ($container === null) {
                throw new NotFoundHttpException();
            }
        }

        return Yii::createObject(['class' => PageTable::class, 'pageType' => $pageType, 'container' => $container]);
    }

    /**
     * @inheritdoc
     * Without the page type and the space's id of the route, which Yii puts among the query
     * parameters.
     */
    protected function listParams(): array
    {
        return array_diff_key(parent::listParams(), ['pageType' => true, 'spaceId' => true]);
    }
}
