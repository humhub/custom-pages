<?php

namespace humhub\modules\custom_pages\controllers;

use humhub\modules\custom_pages\helpers\Url;
use humhub\modules\custom_pages\widgets\ContainerPageMenu;
use humhub\modules\file\models\File;
use Yii;
use yii\web\HttpException;
use humhub\modules\content\components\ContentContainerController;
use humhub\modules\custom_pages\models\ContainerPage;
use humhub\modules\custom_pages\components\Container;
use humhub\modules\custom_pages\models\forms\AddPageForm;
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
            ['class' => TemplateViewBehavior::class],
        ];
    }

    /**
     * Redirects to actionList.
     * @return ContainerController|\yii\console\Response|\yii\web\Response
     */
    public function actionIndex()
    {
        return $this->redirect($this->contentContainer->createUrl('list'));
    }

    /**
     * Provides an overview over all available ContainerPages.
     * @return string
     * @throws HttpException
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function actionList()
    {
        $this->adminOnly();

        $pages = $this->findAll();
        return $this->render('@custom_pages/views/common/list', [
            'pages' => $pages,
            'label' => Yii::createObject($this->getPageClassName())->getLabel(),
            'subNav' => ContainerPageMenu::widget()
        ]);
    }

    /**
     * Is used to view/render a ContainerPage of a certain page content type.
     *
     * This action expects an page id as request parameter.
     *
     * @return string
     * @throws HttpException if the page was not found
     * @throws \yii\base\Exception
     */
    public function actionView()
    {
        $page = ContainerPage::find()->contentContainer($this->contentContainer)->where(['custom_pages_container_page.id' => Yii::$app->request->get('id')])->one();
        return $this->redirect($page->getUrl());
    }

    /**
     * Returns all available ContainerPage models.
     * @return array
     * @throws \yii\base\Exception
     */
    protected function findAll()
    {
        return ContainerPage::find()->contentContainer($this->contentContainer)->all();
    }

    /**
     * Action for adding new ContainerPages.
     * This function can be redelcared by subclasses for supporting other container page types.
     *
     * @return string
     * @throws HttpException
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
                    'subNav' => ContainerPageMenu::widget()]);
    }

    /**
     * Returns the class name of the ContainerPage name. This function can be redelcared by subclasses
     * for supporting other container page types.
     * 
     * @return string
     */
    protected function getPageClassName()
    {
        return ContainerPage::class;
    }

    /**
     * Action for editing ContainerPage models.
     * This action expects either an page id or a content type for creating new pages of a given content type.
     *
     * @param string $type
     * @param integer $id
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function actionEdit($type = null, $id = null)
    {
        $this->adminOnly();

        $page = $this->findPageById($id);

        if (!$page) {
            $page = Yii::createObject($this->getPageClassName());
            $page->type = $type;
            $page->content->container = $this->contentContainer;
        }

        $page->content->visibility = \humhub\modules\content\models\Content::VISIBILITY_PUBLIC;

        if ($page->load(Yii::$app->request->post()) && $page->save()) {
            if ($page->type == Container::TYPE_MARKDOWN) {
                File::attachPrecreated($page, Yii::$app->request->post('fileUploaderHiddenGuidField'));
            }

            return $this->redirect($this->contentContainer->createUrl('list'));
        }

        return $this->render('@custom_pages/views/common/edit', [
                    'page' => $page,
                    'sguid' => $this->space->guid,
                    'subNav' => ContainerPageMenu::widget()]);
    }

    /**
     * Action for deleting ContainerPage models with a given $id.
     *
     * @param int $id page id
     * @return string
     * @throws HttpException
     * @throws \yii\base\Exception
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
     * @param int $id
     * @return string
     * @throws \yii\base\Exception
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
