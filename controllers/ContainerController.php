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
 * Controller for managing ContainerPage instances.
 *
 * @author luke, buddha
 */
class ContainerController extends ContentContainerController
{

    /**
     * @inheritdoc
     * @var boolean 
     */
    public $hideSidebar = true;

    /**
     * @inhritdoc
     */
    public function behaviors()
    {
        return [
            ['class' => TemplateViewBehavior::className()],
        ];
    }

    /**
     * Redirects to actionList.
     * @return type
     */
    public function actionIndex()
    {
        return $this->redirect($this->contentContainer->createUrl('list'));
    }

    /**
     * Is used to view/render a ContainerPage of a certain page content type.
     * 
     * This action expects an page id as request parameter.
     * 
     * @return type
     * @throws HttpException if the page was not found
     */
    public function actionView()
    {
        $page = ContainerPage::find()->contentContainer($this->contentContainer)->where(['custom_pages_container_page.id' => Yii::$app->request->get('id')])->one();

        if ($page === null) {
            throw new HttpException('404', 'Could not find requested page');
        }
        
        $this->getView()->pageTitle = $page->title;

        if ($page->type == Container::TYPE_IFRAME) {
            return $this->render('iframe', array('page' => $page, 'url' => $page->page_content));
        } elseif ($page->type == Container::TYPE_LINK) {
            return $this->redirect($page->page_content);
        } elseif ($page->type == Container::TYPE_MARKDOWN) {
            return $this->render('markdown', array('page' => $page, 'md' => $page->page_content));
        } elseif ($page->type == Container::TYPE_TEMPLATE) {
            return $this->viewTemplatePage($page);
        } else {
            throw new HttpException('500', 'Invalid page type!');
        }
    }

    /**
     * Provides an overview over all available ContainerPages.
     * @return type
     */
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

    /**
     * Returns all available ContainerPage models.
     * @return array
     */
    protected function findAll()
    {
        return ContainerPage::find()->contentContainer($this->contentContainer)->all();
    }

    /**
     * Action for adding new ContainerPages.
     * This function can be redelcared by subclasses for supporting other container page types.
     * 
     * @return type
     */
    public function actionAdd($type = null)
    {
        $this->adminOnly();

        $model = new AddPageForm(['class' => $this->getPageClassName(), 'type' => $type]);

        if ($model->validate()) {
            return $this->redirect($this->contentContainer->createUrl('edit', ['type' => $model->type]));
        }

        return $this->render('@custom_pages/views/common/add', [
                    'model' => $model,
                    'subNav' => \humhub\modules\custom_pages\widgets\ContainerPageMenu::widget()]);
    }

    /**
     * Returns the class name of the ContainerPage name. This function can be redelcared by subclasses
     * for supporting other container page types.
     * 
     * @return string
     */
    protected function getPageClassName()
    {
        return ContainerPage::className();
    }

    /**
     * Action for editing ContainerPage models.
     * This action expects either an page id or a content type for creating new pages of a given content type.
     * 
     * @param type $type
     * @param type $id
     * @return type
     */
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

    /**
     * Action for deleting ContainerPage models with a given $id.
     * 
     * @param type $id page id
     * @return type
     */
    public function actionDelete($id)
    {
        $this->adminOnly();

        $page = $this->findPageById($id);

        if ($page !== null) {
            $page->delete();
        }

        return $this->redirect($this->contentContainer->createUrl('list'));
    }

    /**
     * Searches for a ContainerPage with the given $id.
     * This action expects either an page id or a content type for creating new pages of a given content type.
     * 
     * @param type $id
     * @return type
     */
    protected function findPageById($id = null)
    {
        return ContainerPage::find()->contentContainer($this->contentContainer)->where(['custom_pages_container_page.id' => $id])->one();
    }

    /**
     * Makes sure only admins can execute an action.
     * @throws HttpException
     */
    protected function adminOnly()
    {
        if (!$this->contentContainer->isAdmin()) {
            throw new \yii\web\HttpException('400', 'Access denied!');
        }
    }

}
