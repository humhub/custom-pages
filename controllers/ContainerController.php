<?php

namespace humhub\modules\custom_pages\controllers;

use Yii;
use yii\web\HttpException;
use humhub\modules\content\components\ContentContainerController;
use humhub\modules\custom_pages\models\ContainerPage;
use humhub\modules\custom_pages\components\Container;
use humhub\modules\custom_pages\models\AddPageForm;
use humhub\modules\custom_pages\components\TemplateViewBehavior;

/**
 * Custom Pages for ContentContainer
 *
 * @author luke
 */
class ContainerController extends ContentContainerController
{

    public $hideSidebar = true;
    public $canEdit;

    /**
     * @inhritdoc
     */
    public function behaviors()
    {
        return [
            ['class' => TemplateViewBehavior::className()],
        ];
    }

    public function actionIndex()
    {
        return $this->redirect($this->contentContainer->createUrl('list'));
    }

    public function actionView()
    {
        $page = ContainerPage::find()->contentContainer($this->contentContainer)->where(['custom_pages_container_page.id' => Yii::$app->request->get('id')])->one();

        if ($page === null) {
            throw new HttpException('404', 'Could not find requested page');
        }

        if ($page->type == Container::TYPE_IFRAME) {
            return $this->render('iframe', array('url' => $page->page_content));
        } elseif ($page->type == Container::TYPE_LINK) {
            return $this->redirect($page->page_content);
        } elseif ($page->type == Container::TYPE_MARKDOWN) {
            return $this->render('markdown', array('md' => $page->page_content));
        } elseif ($page->type == Container::TYPE_TEMPLATE) {
            return $this->viewTemplatePage($page);
        } else {
            throw new HttpException('500', 'Invalid page type!');
        }
    }

    public function actionList()
    {
        $this->adminOnly();

        $pages = $this->findAll();
        return $this->render('@custom_pages/views/common/list', [
                    'pages' => $pages,
                    'label' => Yii::createObject($this->getPageClassName())->getLabel(),
                    'subNav' => \humhub\modules\custom_pages\widgets\ContainerPageMenu::widget()
        ]);
    }

    protected function findAll()
    {
        return ContainerPage::find()->contentContainer($this->contentContainer)->all();
    }

    public function actionAdd()
    {
        $this->adminOnly();

        $model = new AddPageForm(['class' => $this->getPageClassName()]);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            return $this->redirect($this->contentContainer->createUrl('edit', ['type' => $model->type]));
        }

        return $this->render('@custom_pages/views/common/add', [
                    'model' => $model,
                    'subNav' => \humhub\modules\custom_pages\widgets\ContainerPageMenu::widget()]);
    }

    protected function getPageClassName()
    {
        return ContainerPage::className();
    }

    public function actionEdit($type = null, $id = null)
    {
        $this->adminOnly();

        $page = $this->findPageById($id);

        if ($page === null) {
            $page = Yii::createObject($this->getPageClassName());
            $page->type = $type;
            $page->content->container = $this->contentContainer;
        }

        $page->content->visibility = \humhub\modules\content\models\Content::VISIBILITY_PUBLIC;

        if ($page->load(Yii::$app->request->post()) && $page->save()) {
            if ($page->type == Container::TYPE_MARKDOWN) {
                \humhub\modules\file\models\File::attachPrecreated($page, Yii::$app->request->post('fileUploaderHiddenGuidField'));
            }

            return $this->redirect($this->contentContainer->createUrl('list'));
        }

        return $this->render('@custom_pages/views/common/edit', [
                    'page' => $page,
                    'sguid' => $this->space->guid,
                    'subNav' => \humhub\modules\custom_pages\widgets\ContainerPageMenu::widget()]);
    }

    public function actionDelete($id)
    {
        $this->adminOnly();

        $page = $this->findPageById($id);

        if ($page !== null) {
            $page->delete();
        }

        return $this->redirect($this->contentContainer->createUrl('list'));
    }

    protected function findPageById($id = null)
    {
        return ContainerPage::find()->contentContainer($this->contentContainer)->where(['custom_pages_container_page.id' => $id])->one();
    }

    protected function adminOnly()
    {
        if (!$this->contentContainer->isAdmin()) {
            throw new \yii\web\HttpException('400', 'Access denied!');
        }
    }

}
