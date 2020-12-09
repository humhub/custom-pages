<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2019 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\controllers;

use humhub\components\access\StrictAccess;
use humhub\modules\admin\permissions\ManageModules;
use humhub\modules\content\components\ContentContainerController;
use humhub\modules\custom_pages\models\ContainerPage;
use humhub\modules\custom_pages\models\ContainerSnippet;
use humhub\modules\custom_pages\models\CustomContentContainer;
use humhub\modules\custom_pages\models\Page;
use humhub\modules\custom_pages\models\PageType;
use humhub\modules\custom_pages\models\Snippet;
use humhub\modules\custom_pages\modules\template\components\TemplateCache;
use humhub\modules\custom_pages\modules\template\models\TemplateInstance;
use humhub\modules\custom_pages\modules\template\models\PagePermission;
use humhub\modules\custom_pages\permissions\ManagePages;
use humhub\modules\space\models\Space;
use Yii;
use yii\web\HttpException;

abstract class AbstractCustomContainerController extends ContentContainerController
{
    /**
     * @inheritdoc
     */
    public $requireContainer = false;

    private $_canEdit;

    /**
     * Default access used when $requireContainer = false and no container is given.
     * @var string
     */
    public $access = StrictAccess::class;

    public function init()
    {
        Yii::$app->moduleManager->getModule('custom_pages')->checkOldGlobalContent();
        parent::init();
    }

    /**
     * @return string
     * @see PageType
     */
    abstract protected function getPageType();

    /**
     * Returns the actual class for this type of page.
     *
     * @return string
     */
    protected function getPageClassName()
    {
        if($this->getPageType() === PageType::Snippet) {
            return $this->contentContainer ? ContainerSnippet::class : Snippet::class;
        }

        return $this->contentContainer ? ContainerPage::class : Page::class;
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

    /**
     *
     * @param \humhub\modules\custom_pages\models\CustomContentContainer $page
     * @return string rendered template page
     * @throws \yii\web\HttpException in case the page is protected from non admin access
     */
    public function viewTemplatePage(CustomContentContainer $page, $view)
    {
        $html = $this->renderTemplate($page);
        $canEdit = $this->isCanEdit();

        if(!$canEdit && $page->admin_only) {
            throw new \yii\web\HttpException(403, 'Access denied!');
        }

        return $this->owner->render('template', [
            'page' => $page,
            'editMode' => Yii::$app->request->get('editMode') && $canEdit,
            'canEdit' => $canEdit,
            'html' => $html
        ]);
    }

    /**
     * @param $page
     * @param null $editMode
     * @return string
     * @throws HttpException
     */
    public function renderTemplate($page, $editMode = null)
    {
        $templateInstance = TemplateInstance::findOne(['object_model' => get_class($page) ,'object_id' => $page->id]);

        if(!$templateInstance) {
            throw new HttpException(404);
        }

        $canEdit = PagePermission::canEdit();
        $editMode = ($editMode || Yii::$app->request->get('editMode')) && $canEdit;

        $html = '';

        if(!$canEdit && TemplateCache::exists($templateInstance)) {
            $html = TemplateCache::get($templateInstance);
        } else {
            $html = $templateInstance->render($editMode);
            if(!$canEdit) {
                TemplateCache::set($templateInstance, $html);
            }
        }
        return $html;
    }

    /**
     * @return bool
     */
    protected function adminOnly()
    {
        if($this->contentContainer instanceof Space) {
            return $this->contentContainer->isAdmin();
        }

        return Yii::$app->user->isAdmin() || Yii::$app->user->can([ManageModules::class, ManagePages::class]);
    }

    public function isCanEdit() {
        if($this->_canEdit === null) {
            $this->_canEdit = PagePermission::canEdit();
        }
        return $this->_canEdit;
    }

}