<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\controllers\rest;

use humhub\modules\content\components\ContentActiveRecord;
use humhub\modules\content\models\Content;
use humhub\modules\custom_pages\helpers\RestDefinitions;
use humhub\modules\custom_pages\models\CustomPage;
use humhub\modules\rest\components\BaseContentController;
use Yii;

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

    private function findCustomPages(?int $contentContainerId = null)
    {
        $query = CustomPage::find()->joinWith('content')->orderBy(['content.created_at' => SORT_DESC])->readable();

        if ($contentContainerId !== null) {
            $query->andWhere([Content::tableName() . '.contentcontainer_id' => $contentContainerId ?: null]);
        }

        $pagination = $this->handlePagination($query);

        $results = [];
        foreach ($query->all() as $contentRecord) {
            $results[] = $this->returnContentDefinition($contentRecord);
        }

        return $this->returnPagination($query, $pagination, $results);
    }

    public function actionCreate($containerId)
    {
        return $this->notAllowed();
    }

    public function actionCreateGlobal()
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