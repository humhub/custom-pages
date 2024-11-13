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
use humhub\modules\custom_pages\helpers\Html;
use humhub\modules\custom_pages\models\CustomPage;
use humhub\modules\custom_pages\helpers\PageType;
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
    abstract protected function getPageType(): string;

    /**
     * Returns a page by a given $id.
     *
     * @param int $id page id.
     * @return CustomPage
     */
    protected function findById($id)
    {
        return CustomPage::findOne(['id' => $id]);
    }

    /**
     * Render the given template page
     *
     * @param CustomPage $page
     * @return string rendered template page
     * @throws \yii\web\HttpException in case the page is protected from non admin access
     */
    public function viewTemplatePage(CustomPage $page, $view): string
    {
        $html = $this->renderTemplate($page);
        $canEdit = $this->isCanEdit();

        if (!$canEdit && $page->admin_only) {
            throw new \yii\web\HttpException(403, 'Access denied!');
        }

        return $this->owner->render('template', [
            'page' => $page,
            'editMode' => Yii::$app->request->get('editMode') && $canEdit,
            'canEdit' => $canEdit,
            'html' => $html,
        ]);
    }

    /**
     * @param CustomPage $page
     * @param bool $editMode
     * @return string
     * @throws HttpException
     */
    public function renderTemplate(CustomPage $page, $editMode = false)
    {
        $templateInstance = TemplateInstance::findOne(['page_id' => $page->id]);

        if (!$templateInstance) {
            throw new HttpException(404);
        }

        $canEdit = PagePermission::canEdit();
        $editMode = ($editMode || Yii::$app->request->get('editMode')) && $canEdit;

        if (!$canEdit && TemplateCache::exists($templateInstance)) {
            $html = TemplateCache::get($templateInstance);
        } else {
            $html = $templateInstance->render($editMode);
            if (!$canEdit) {
                TemplateCache::set($templateInstance, $html);
            }
        }

        return Html::applyScriptNonce($html);
    }

    /**
     * @return bool
     */
    protected function adminOnly()
    {
        if ($this->contentContainer instanceof Space) {
            return $this->contentContainer->isAdmin();
        }

        return Yii::$app->user->isAdmin() || Yii::$app->user->can([ManageModules::class, ManagePages::class]);
    }

    public function isCanEdit()
    {
        if ($this->_canEdit === null) {
            $this->_canEdit = PagePermission::canEdit();
        }
        return $this->_canEdit;
    }

}
