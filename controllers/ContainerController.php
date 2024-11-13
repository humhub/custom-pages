<?php

namespace humhub\modules\custom_pages\controllers;

use humhub\modules\custom_pages\models\Page;
use yii\web\HttpException;
use humhub\modules\content\components\ContentContainerController;
use yii\web\NotFoundHttpException;

/**
 * Controller for managing Container/Space Page instances.
 *
 * @author luke, buddha
 * @deprecated
 */
class ContainerController extends ContentContainerController
{
    /**
     * Is used to view/render a Container/Space Page of a certain page content type.
     *
     * This action expects a page id as request parameter.
     *
     * @return string
     * @throws HttpException if the page was not found
     * @throws \yii\base\Exception
     */
    public function actionView($id)
    {
        /* @var Page $page */
        $page = Page::find()
            ->contentContainer($this->contentContainer)
            // TODO: Filter only with Space targets
            ->andWhere([Page::tableName() . '.id' => $id])
            ->one();

        if (!$page) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        return $this->redirect($page->getUrl());
    }
}
