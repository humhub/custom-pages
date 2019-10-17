<?php

namespace humhub\modules\custom_pages\controllers;

use yii\web\HttpException;
use humhub\modules\content\components\ContentContainerController;
use humhub\modules\custom_pages\models\ContainerPage;

/**
 * Controller for managing ContainerPage instances.
 *
 * @author luke, buddha
 * @deprecated
 */
class ContainerController extends ContentContainerController
{

    /**
     * Is used to view/render a ContainerPage of a certain page content type.
     *
     * This action expects an page id as request parameter.
     *
     * @return string
     * @throws HttpException if the page was not found
     * @throws \yii\base\Exception
     */
    public function actionView($id)
    {
        $page = ContainerPage::find()->contentContainer($this->contentContainer)->where(['custom_pages_container_page.id' => $id])->one();
        if(!$page) {
            throw new HttpException(404);
        }
        return $this->redirect($page->getUrl());
    }
}
