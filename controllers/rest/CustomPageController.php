<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\controllers\rest;

use humhub\modules\content\components\ContentActiveRecord;
use humhub\modules\content\models\Content;
use humhub\modules\content\models\ContentContainer;
use humhub\modules\custom_pages\helpers\RestDefinitions;
use humhub\modules\custom_pages\models\CustomPage;
use humhub\modules\custom_pages\permissions\ManagePages;
use humhub\modules\rest\components\BaseContentController;
use Yii;
use yii\db\ActiveQuery;

class CustomPageController extends BaseContentController
{
    public static $moduleId = 'custom_pages';

    /**
     * {@inheritdoc}
     */
    public function getContentActiveRecordClass()
    {
        return CustomPage::class;
    }

    /**
     * {@inheritdoc}
     */
    public function returnContentDefinition(ContentActiveRecord $contentRecord)
    {
        /** @var CustomPage $contentRecord */
        return RestDefinitions::getCustomPage($contentRecord);
    }

    /**
     * {@inheritdoc}
     *
     * Overridden because CustomPage has its own visibility model (public/private/admin-only/
     * custom group- or language-restricted, see {@see \humhub\modules\custom_pages\services\VisibilityService})
     * which is independent of the generic {@see Content::canView()} used by the parent implementation.
     */
    public function actionView($id)
    {
        $contentRecord = CustomPage::findOne(['id' => $id]);

        if ($contentRecord === null) {
            return $this->returnError(404, 'Requested content not found!');
        }

        if (!$this->canViewPage($contentRecord)) {
            return $this->returnError(403, 'You cannot view this content!');
        }

        return $this->returnContentDefinition($contentRecord);
    }

    public function actionFind()
    {
        $contentContainerId = Yii::$app->request->get('contentcontainer_id');

        if ($contentContainerId !== null) {
            if (!ctype_digit((string)$contentContainerId)) {
                return $this->returnError(400, 'Invalid contentcontainer_id parameter.');
            }
        }

        return $this->findCustomPages($contentContainerId === null ? null : (int)$contentContainerId);
    }

    public function actionFindGlobal()
    {
        return $this->findCustomPages(0);
    }

    /**
     * {@inheritdoc}
     *
     * Overridden to additionally filter out pages the current user is not allowed to view according to
     * CustomPage's own visibility rules (see {@see self::canViewPage()}).
     */
    public function actionFindByContainer($containerId)
    {
        $contentContainer = ContentContainer::findOne(['id' => $containerId]);
        if ($contentContainer === null) {
            return $this->returnError(404, 'Content container not found!');
        }

        $query = CustomPage::find()
            ->contentContainer($contentContainer->getPolymorphicRelation())
            ->orderBy(['content.created_at' => SORT_DESC])
            ->readable();

        return $this->returnFilteredPagination($query);
    }

    private function findCustomPages(?int $contentContainerId = null)
    {
        $query = CustomPage::find()->joinWith('content')->orderBy(['content.created_at' => SORT_DESC])->readable();

        if ($contentContainerId !== null) {
            $query->andWhere([Content::tableName() . '.contentcontainer_id' => $contentContainerId ?: null]);
        }

        return $this->returnFilteredPagination($query);
    }

    /**
     * Runs the given, already `readable()`-scoped query through the standard
     * {@see BaseContentController::handlePagination()} and additionally drops every record the
     * current user is not allowed to view according to CustomPage's own visibility rules
     * (admin-only / guest-only / custom group- or language-restricted pages), which are
     * independent of the generic {@see Content::canView()} that `readable()` is built on -
     * see {@see self::canViewPage()}.
     *
     * Note: `total`/`pages` are computed by `handlePagination()` before this filter runs, and
     * filtered-out records are simply skipped rather than backfilled from the next page. So a
     * page can come back with fewer than `limit` results (or, rarely, empty) when restricted
     * pages are mixed into it - deliberately accepted here for simplicity over exact pagination.
     *
     * @param ActiveQuery $query
     * @return array
     */
    private function returnFilteredPagination(ActiveQuery $query): array
    {
        $pagination = $this->handlePagination($query);

        $results = [];
        foreach ($query->all() as $contentRecord) {
            /** @var CustomPage $contentRecord */
            if ($this->canViewPage($contentRecord)) {
                $results[] = $this->returnContentDefinition($contentRecord);
            }
        }

        return $this->returnPagination($query, $pagination, $results);
    }

    /**
     * Checks whether the current user is allowed to view the given page, taking into account
     * CustomPage's own visibility model (admin-only / custom group- or language-restricted pages),
     * mirroring the check done in {@see \humhub\modules\custom_pages\controllers\ViewController::actionIndex()}.
     *
     * @param CustomPage $page
     * @return bool
     */
    private function canViewPage(CustomPage $page): bool
    {
        return Yii::$app->user->can([ManagePages::class]) || $page->canView();
    }

    public function actionCreate($containerId)
    {
        return $this->notAllowed();
    }

    public function actionUpdate($id)
    {
        return $this->notAllowed();
    }

    public function actionDelete($id)
    {
        return $this->notAllowed();
    }

    public function actionDeleteByContainer($containerId)
    {
        return $this->notAllowed();
    }

    public function actionAttachFiles($id)
    {
        return $this->notAllowed();
    }

    public function actionRemoveFile($id, $fileId)
    {
        return $this->notAllowed();
    }

    private function notAllowed()
    {
        return $this->returnError(405, 'Method not allowed. Read-only endpoint.');
    }
}
