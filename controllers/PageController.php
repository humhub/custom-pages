<?php

namespace humhub\modules\custom_pages\controllers;

use humhub\modules\admin\permissions\ManageModules;
use humhub\modules\content\models\Content;
use humhub\modules\content\models\ContentContainer;
use humhub\modules\custom_pages\models\TemplateType;
use humhub\modules\custom_pages\permissions\ManagePages;
use Yii;
use humhub\modules\custom_pages\models\CustomContentContainer;
use humhub\modules\custom_pages\models\PageType;
use humhub\modules\custom_pages\helpers\Url;
use humhub\modules\custom_pages\interfaces\CustomPagesService;
use humhub\components\access\ControllerAccess;
use humhub\modules\content\components\ContentContainerControllerAccess;
use humhub\modules\space\models\Space;
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
class PageController extends AbstractCustomContainerController
{
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
        if ($this->getContainerFromRequest() instanceof Space) {
            return [
                [ContentContainerControllerAccess::RULE_USER_GROUP_ONLY => [Space::USERGROUP_ADMIN]],
            ];
        }

        return [
            ['permissions' => [ManageModules::class, ManagePages::class]]
        ];
    }

    /**
     * This is a patch for https://github.com/humhub/humhub/issues/4844
     * @return mixed|\yii\db\ActiveRecord|null
     * @throws \yii\db\IntegrityException
     */
    private function getContainerFromRequest()
    {
        $guid = Yii::$app->request->get('cguid', Yii::$app->request->get('sguid', Yii::$app->request->get('uguid')));
        if (!empty($guid)) {
            $contentContainerModel = ContentContainer::findOne(['guid' => $guid]);
            if ($contentContainerModel !== null) {
                return $contentContainerModel->getPolymorphicRelation();
            }
        }

        return null;
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
     * @throws \yii\base\Exception
     * @throws \Exception
     * @throws \Throwable
     */
    public function actionEdit($targetId = null, $type = null, $id = null)
    {
        /* @var CustomContentContainer $page*/
       $page = $this->findByid($id);

        if (!$page && !$targetId) {
            throw new HttpException(400, 'Invalid request data!');
        }

        // If no pageId was given, we create a new page with the given type.
        if (!$page) {
            $page = $this->createNewPage($type, $targetId);
        }

        $isNew = $page->isNewRecord;

        if($this->savePage($page)) {
            return (TemplateType::isType($type) && $isNew)
                ? $this->redirect(Url::toInlineEdit($page, $this->contentContainer))
                : $this->redirect(Url::toOverview($this->getPageType(), $this->contentContainer));
        }

        // Select a proper option on the edit form for old stored page
        // if its visibility is not allowed for its page type:
        $page->fixVisibility();

        return $this->render('@custom_pages/views/common/edit', [
            'page' => $page,
            'pageType' => $this->getPageType(),
            'subNav' => $this->getSubNav()
        ]);
    }

    /**
     * @param $page CustomContentContainer
     * @return bool
     * @throws \Throwable
     * @throws \yii\db\Exception
     */
    protected function savePage($page)
    {
        if(!$page->load(Yii::$app->request->post())) {
            return false;
        }
        $transaction = Page::getDb()->beginTransaction();

        try {
            $saved = $page->save();
            $transaction->commit();
        } catch(\Exception $e) {
            $transaction->rollBack();
            throw $e;
        } catch(\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }

        return $saved;
    }

    /**
     * @param $type
     * @param $targetId
     * @return CustomContentContainer
     */
    private function createNewPage($type, $targetId)
    {
        $pageClass = $this->getPageClassName();
        $page = new $pageClass(['type' => $type, 'target' => $targetId]);
        if($this->contentContainer) {
            $page->content->setContainer($this->contentContainer);
            if(!$this->contentContainer) {
                $page->content->visibility = Content::VISIBILITY_PUBLIC;
            }
        }
        return $page;
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

        if ($page) {
            $page->delete();
        }

        return $this->redirect(Url::toOverview($this->getPageType(), $this->contentContainer));
    }

    /**
     * @inheritdoc
     */
    protected function getPageType()
    {
        return PageType::Page;
    }
}
