<?php

namespace humhub\modules\custom_pages\controllers;

use humhub\modules\admin\components\Controller;
use humhub\modules\custom_pages\models\forms\SettingsForm;
use humhub\modules\custom_pages\widgets\AdminMenu;
use Yii;
use humhub\modules\custom_pages\models\Page;
use humhub\modules\custom_pages\components\Container;
use humhub\modules\custom_pages\models\forms\AddPageForm;
use humhub\modules\file\models\File;
use yii\data\Pagination;
use yii\web\HttpException;

/**
 * AdminController used to manage global (non container) pages of type humhub\modules\custom_pages\models\Page.
 * 
 * This Controller is designed to be overwritten by other controller for supporting other page types. 
 * 
 * The following functions have to be redeclared in order to support another page type:
 * 
 *  - findAll()
 *  - getPageClassName()
 *  - findById()
 * 
 * @author luke, buddha
 */
class AdminController extends Controller
{

    public function actionIndex()
    {
        return $this->runAction('pages');
    }

    public function actionSettings()
    {
        $model = new SettingsForm();

        if($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->view->saved();
        }

        return $this->render('settings', [
            'model' => $model,
            'subNav' => AdminMenu::widget()
        ]);
    }

    /**
     * Returns a view which lists all available pages of a given type.
     *
     * @see getPageClassName() which returns the actual page type.
     * @return string view
     */
    public function actionPages()
    {
        return $this->render('@custom_pages/views/common/list', [
            'pages' => $this->findAll(),
            'label' => Yii::createObject($this->getPageClassName())->getLabel(),
            'subNav' => AdminMenu::widget()
        ]);
    }

    /**
     * This action is used to add a new page of a given type.
     * After selecting a page content type the user is redirected to an edit page view.
     *
     * @see getPageClassName() which returns the actual page type.
     * @param integer $type
     * @return string view
     */
    public function actionAdd($type = null)
    {
        $model = new AddPageForm(['class' => $this->getPageClassName(), 'type' => $type]);

        if ($model->validate()) {
            return $this->redirect(['edit', 'type' => $model->type]);
        }

        return $this->render('@custom_pages/views/common/add', [
                    'model' => $model,
                    'subNav' => AdminMenu::widget()
        ]);
    }

    /**
     * Action for editing pages. This action expects either an page id or a content type for
     * creating new pages of a given content type.
     * 
     * @see getPageClassName() which returns the actual page type.
     * @param integer $type
     * @param integer $id
     * @return string
     */
    public function actionEdit($type = null, $id = null)
    {   
        $page = $this->findByid($id);

        if($page == null && $type == null) {
            throw new HttpException(400, 'Invalid request data!');
        }
        
        // If no pageId was given, we create a new page with the given type.
        if ($page == null && $type != null) {
            $page = Yii::createObject($this->getPageClassName());
            $page->type = $type;
        }

        if ($page->load(Yii::$app->request->post()) && $page->save()) {
            if ($page->type == Container::TYPE_MARKDOWN) {
                File::attachPrecreated($page, Yii::$app->request->post('fileUploaderHiddenGuidField'));
            }

            return $this->redirect(['pages']);
        }

        return $this->render('@custom_pages/views/common/edit', [
                    'page' => $page,
                    'subNav' => AdminMenu::widget()
        ]);
    }

    /**
     * Deltes the page with a given $id.
     * 
     * @param integer $id page id
     * @return string
     */
    public function actionDelete($id)
    {
        $page = $this->findByid($id);

        if ($page !== null) {
            $page->delete();
        }

        return $this->redirect(['index']);
    }

    /**
     * Returns all page instances. This method has to be overwritten by subclasses
     * supporting another page type.
     * 
     * @return array|Page[]
     */
    protected function findAll()
    {
        return Page::find()->all();
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
        return Page::className();
    }
    
    /**
     * Returns a page by a given $id.
     * 
     * @param integer $id page id.
     * @return Page
     */
    protected function findById($id)
    {
        return Page::findOne(['id' => $id]);
    }

}
