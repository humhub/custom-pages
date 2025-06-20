<?php

namespace humhub\modules\custom_pages\controllers;

use humhub\modules\admin\permissions\ManageModules;
use humhub\modules\content\models\Content;
use humhub\modules\content\models\ContentContainer;
use humhub\modules\custom_pages\models\CustomPage;
use humhub\modules\custom_pages\types\TemplateType;
use humhub\modules\custom_pages\permissions\ManagePages;
use humhub\modules\custom_pages\helpers\PageType;
use humhub\modules\custom_pages\helpers\Url;
use humhub\modules\custom_pages\interfaces\CustomPagesService;
use humhub\modules\content\components\ContentContainerControllerAccess;
use humhub\modules\space\models\Space;
use humhub\modules\custom_pages\widgets\AdminMenu;
use humhub\modules\custom_pages\models\forms\AddPageForm;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

/**
 * PageController used to manage global (non container) pages.
 *
 * This Controller is designed to be overwritten by other controller for supporting other page types.
 *
 * @author luke, buddha
 */
class PageController extends AbstractCustomContainerController
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (!$this->contentContainer) {
            $this->subLayout = "@humhub/modules/admin/views/layouts/main";
        }
    }

    /**
     * @inheritdoc
     */
    protected function getAccessRules()
    {
        if ($this->getContainerFromRequest() instanceof Space) {
            return [
                [ContentContainerControllerAccess::RULE_USER_GROUP_ONLY => [Space::USERGROUP_ADMIN]],
            ];
        }

        return [
            ['permissions' => [ManageModules::class, ManagePages::class]],
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
     * @return string view
     * @throws \Exception
     */
    public function actionOverview()
    {
        return $this->render('@custom_pages/views/common/list', [
            'targets' => CustomPagesService::instance()->getTargets($this->getPageType(), $this->contentContainer),
            'pageType' => $this->getPageType(),
            'subNav' => $this->getSubNav(),
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
     * @param string $targetId
     * @param int $type
     * @return string view
     * @throws \Exception
     */
    public function actionAdd($targetId, $type = null)
    {
        $target = CustomPagesService::instance()->getTargetById($targetId, $this->getPageType(), $this->contentContainer);

        if (!$target) {
            throw new HttpException(404, 'Invalid target setting!');
        }

        $model = new AddPageForm(['target' => $target, 'type' => $type]);

        if ($model->validate()) {
            return $this->redirect(Url::toCreatePage($targetId, $this->getPageType(), $type, $this->contentContainer));
        }

        return $this->render('@custom_pages/views/common/add', [
            'model' => $model,
            'target' => $target,
            'pageType' => $this->getPageType(),
            'subNav' => $this->getSubNav(),
        ]);
    }

    /**
     * Action for editing pages. This action expects either a page id or a content type for
     * creating new pages of a given content type.
     *
     * @param null $targetId
     * @param int $type content type
     * @param int $id
     * @return string
     * @throws HttpException
     * @throws \yii\base\Exception
     * @throws \Exception
     * @throws \Throwable
     */
    public function actionEdit($targetId = null, $type = null, $id = null)
    {
        /* @var CustomPage $page*/
        $page = $this->findByid($id);

        if (!$page && !$targetId) {
            throw new HttpException(400, 'Invalid request data!');
        }

        // If no pageId was given, we create a new page with the given type.
        if (!$page) {
            $page = $this->createNewPage($type, $targetId);
        }

        if (!$page->canEdit()) {
            throw new ForbiddenHttpException('You cannot manage the page!');
        }

        $isNew = $page->isNewRecord;

        if ($this->savePage($page)) {
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
            'subNav' => $this->getSubNav(),
        ]);
    }

    /**
     * @param $page CustomPage
     * @return bool
     * @throws \Throwable
     * @throws \yii\db\Exception
     */
    protected function savePage($page)
    {
        if (!$page->load(Yii::$app->request->post())) {
            return false;
        }

        $transaction = CustomPage::getDb()->beginTransaction();

        try {
            $saved = $page->save();
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }

        return $saved;
    }

    /**
     * Action for copying pages.
     *
     * @param int $id
     */
    public function actionCopy($id)
    {
        $sourcePage = $this->findByid($id);

        if (!$sourcePage) {
            throw new BadRequestHttpException('Invalid request data!');
        }

        if (!$sourcePage->canEdit()) {
            throw new ForbiddenHttpException('You cannot manage the page!');
        }

        $copyPage = CustomPagesService::instance()->duplicatePage($sourcePage, Yii::$app->request->post());

        if ($copyPage && !$copyPage->isNewRecord) {
            return (TemplateType::isType($copyPage->type))
                ? $this->redirect(Url::toInlineEdit($copyPage, $this->contentContainer))
                : $this->redirect(Url::toOverview($this->getPageType(), $this->contentContainer));
        }

        return $this->render('@custom_pages/views/common/edit', [
            'page' => $copyPage,
            'pageType' => $this->getPageType(),
            'subNav' => $this->getSubNav(),
        ]);
    }

    /**
     * @param $type
     * @param $targetId
     * @return CustomPage
     */
    private function createNewPage($type, $targetId): CustomPage
    {
        $page = new CustomPage(['type' => $type, 'target' => $targetId]);
        if ($this->contentContainer) {
            $page->content->setContainer($this->contentContainer);
            if (!$this->contentContainer) {
                $page->content->visibility = Content::VISIBILITY_PUBLIC;
            }
        }
        return $page;
    }

    /**
     * Deletes the page with a given $id.
     *
     * @param int $id page id
     * @param bool $irrevocably
     * @return string
     * @throws HttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $this->forcePostRequest();

        $page = $this->findByid($id);

        if (!$page) {
            throw new NotFoundHttpException();
        }

        if (!$page->content->canEdit()) {
            throw new ForbiddenHttpException();
        }

        if ($page->delete()) {
            $this->view->success(Yii::t('CustomPagesModule.base', 'Deleted.'));
        } else {
            $this->view->error(Yii::t('CustomPagesModule.base', 'Cannot delete!'));
        }

        return $this->redirect(Url::toOverview($this->getPageType(), $this->contentContainer));
    }

    /**
     * @inheritdoc
     */
    protected function getPageType(): string
    {
        return PageType::Page;
    }
}
