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
use humhub\modules\custom_pages\interfaces\CustomPagesService;
use humhub\modules\custom_pages\models\CustomPage;
use humhub\modules\custom_pages\helpers\PageType;
use humhub\modules\custom_pages\modules\template\helpers\PagePermissionHelper;
use humhub\modules\custom_pages\modules\template\services\TemplateInstanceRendererService;
use humhub\modules\custom_pages\permissions\ManagePages;
use humhub\modules\space\models\Space;
use Yii;
use yii\web\ForbiddenHttpException;

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
     * @return CustomPage|null
     */
    protected function findById($id): ?CustomPage
    {
        return CustomPagesService::instance()
            ->findByPageType($this->getPageType(), $this->contentContainer)
            ->andWhere([CustomPage::tableName() . '.id' => $id])
            ->one();
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
        $canEdit = PagePermissionHelper::canEdit();
        if (!$canEdit && $page->admin_only) {
            throw new ForbiddenHttpException('Access denied!');
        }

        $mode = $canEdit ? Yii::$app->request->get('mode', '') : '';

        return $this->owner->render('template', [
            'page' => $page,
            'canEdit' => $canEdit,
            'html' => TemplateInstanceRendererService::instance($page, $mode === 'edit')->render(),
        ]);
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
}
