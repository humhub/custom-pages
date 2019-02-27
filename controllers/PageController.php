<?php

namespace humhub\modules\custom_pages\controllers;

use humhub\modules\custom_pages\models\TemplateType;
use Yii;
use humhub\modules\custom_pages\models\CustomContentContainer;
use humhub\modules\custom_pages\models\PageType;
use humhub\modules\custom_pages\helpers\Url;
use humhub\modules\custom_pages\interfaces\CustomPagesService;
use humhub\modules\custom_pages\models\ContainerPage;
use humhub\modules\custom_pages\widgets\ContainerPageMenu;
use humhub\components\access\ControllerAccess;
use humhub\modules\content\components\ContentContainerControllerAccess;
use humhub\modules\space\models\Space;
use humhub\modules\content\components\ContentContainerController;
use humhub\modules\custom_pages\widgets\AdminMenu;
use humhub\modules\custom_pages\models\Page;
use humhub\modules\custom_pages\models\forms\AddPageForm;
use yii\web\HttpException;

/**
 * PageController used to manage global (non container) pages of type humhub\modules\custom_pages\models\Page.
 *
 * This Controller is designed to be overwritten by other controller for supporting other page types.
 *
 * The following functions have to be redeclared in order to support another page type:
 *
 *  - getPageClassName()
 *  - findById()
 *
 * @author luke, buddha
 */
class PageController extends ContentContainerController
{
    /**
     * @inheritdoc
     */
    public $requireContainer = false;

    /**
     * @var CustomPagesService
     */
    public $customPagesService;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->customPagesService = new CustomPagesService();
        if (!$this->contentContainer) {
            $this->subLayout = "@humhub/modules/admin/views/layouts/main";
        }
    }

    /**
     * @inheritdoc
     */
    public function getAccessRules()
    {
        if ($this->contentContainer) {
            return [
                [ContentContainerControllerAccess::RULE_USER_GROUP_ONLY => [Space::USERGROUP_ADMIN]],
            ];
        }

        return [
            [ControllerAccess::RULE_ADMIN_ONLY]
        ];
    }

    /**
     * @return mixed
     * @throws \yii\base\InvalidRouteException
     */
    public function actionIndex()
    {
        return $this->runAction('overview');
    }

    /**
     * Returns a view which lists all available pages of a given type.
     *
     * @see getPageClassName() which returns the actual page type.
     * @return string view
     * @throws \Exception
     */
    public function actionOverview()
    {
        return $this->render('@custom_pages/views/common/list', [
            'targets' => $this->customPagesService->getTargets($this->getPageType(), $this->contentContainer),
            'pageType' => $this->getPageType(),
            'subNav' => $this->getSubNav()
        ]);
    }

    /**
     * @return string
     * @throws \Exception
     */
    private function getSubNav()
    {
        return $this->contentContainer ? '' : AdminMenu::widget();
    }

    /**
     * This action is used to add a new page of a given type.
     * After selecting a page content type the user is redirected to an edit page view.
     *
     * @see getPageClassName() which returns the actual page type.
     * @param string $targetId
     * @param integer $type
     * @return string view
     * @throws \Exception
     */
    public function actionAdd($targetId, $type = null)
    {
        $target = $this->customPagesService->getTargetById($targetId, $this->getPageType(), $this->contentContainer);

        if (!$target) {
            throw new HttpException(404, 'Invalid target setting!');
        }

        $model = new AddPageForm(['class' => $this->getPageClassName(), 'target' => $target, 'type' => $type]);

        if ($model->validate()) {
            return $this->redirect(Url::toCreatePage($targetId, $this->getPageType(), $type, $this->contentContainer));
        }

        return $this->render('@custom_pages/views/common/add', [
            'model' => $model,
            'target' => $target,
            'pageType' => $this->getPageType(),
            'subNav' => $this->getSubNav()
        ]);
    }

    /**
     * Action for editing pages. This action expects either an page id or a content type for
     * creating new pages of a given content type.
     *
     * @see getPageClassName() which returns the actual page type.
     * @param null $targetId
     * @param integer $type content type
     * @param integer $id
     * @return string
     * @throws HttpException
     * @throws \yii\base\InvalidRouteException
     */
    public function actionEdit($targetId = null, $type = null, $id = null)
    {
        /* @var CustomContentContainer $page*/
       $page = $this->findByid($id);
       $isNew = false;

        if (!$page && !$targetId) {
            throw new HttpException(400, 'Invalid request data!');
        }

        // If no pageId was given, we create a new page with the given type.
        if (!$page) {
            $isNew = true;
            $pageClass = $this->getPageClassName();
            $page = new $pageClass($this->contentContainer, ['type' => $type, 'target' => $targetId]);
        }

        if ($page->load(Yii::$app->request->post()) && $page->save()) {
            return (TemplateType::isType($type) && $isNew)
                ? $this->redirect(Url::toInlineEdit($page, $this->contentContainer))
                : $this->runAction('overview');
        }

        return $this->render('@custom_pages/views/common/edit', [
            'page' => $page,
            'pageType' => $this->getPageType(),
            'subNav' => $this->getSubNav()
        ]);
    }

    /**
     * Deltes the page with a given $id.
     *
     * @param integer $id page id
     * @return string
     * @throws HttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $this->forcePostRequest();

        $page = $this->findByid($id);

        if ($page !== null) {
            $page->delete();
        }

        return $this->redirect(Url::toOverview($this->getPageType(), $this->contentContainer));
    }

    /**
     * Returns the class name of the supported page type.
     * Default page class is humhub\modules\custom_pages\models\Page.
     *
     * This method has to be overwritten by subclasses supporting another page type.
     *
     * @return string
     */
    protected function getPageClassName()
    {
        return $this->contentContainer ? ContainerPage::class : Page::class;
    }

    protected function getPageType()
    {
        return PageType::Page;
    }

    /**
     * Returns a page by a given $id.
     *
     * @param integer $id page id.
     * @return CustomContentContainer
     */
    protected function findById($id)
    {
        return call_user_func($this->getPageClassName().'::findOne', ['id' => $id]);
    }

}
