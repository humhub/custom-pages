<?php

/**
 * HumHub
 * Copyright © 2014 The HumHub Project
 *
 * The texts of the GNU Affero General Public License with an additional
 * permission and of our proprietary license can be found at and
 * in the LICENSE file you have received along with this program.
 *
 * According to our dual licensing model, this program can be used either
 * under the terms of the GNU Affero General Public License, version 3,
 * or under a proprietary license.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 */

/**
 * Description of AdminController
 *
 * @author luke
 */
class AdminController extends Controller
{

    public $subLayout = "application.modules_core.admin.views._layout";

    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(
            array('allow',
                'expression' => 'Yii::app()->user->isAdmin()',
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    public function actionIndex()
    {
        $pages = CustomPage::model()->findAll();
        $this->render('index', array('pages' => $pages));
    }

    public function actionEdit()
    {
        $page = CustomPage::model()->findByPk(Yii::app()->request->getParam('id'));

        if ($page === null) {
            $page = new CustomPage;
        }

        if (isset($_POST['ajax']) && $_POST['ajax'] === 'page-edit-form') {
            echo CActiveForm::validate($page);
            Yii::app()->end();
        }

        Yii::app()->clientScript->registerCssFile($this->getModule()->getAssetsUrl() . '/bootstrap-select.min.css');
        Yii::app()->clientScript->registerScriptFile($this->getModule()->getAssetsUrl() . '/bootstrap-select.min.js');

        if (isset($_POST['CustomPage'])) {
            $page->attributes = $_POST['CustomPage'];

            if ($page->validate()) {
                $page->save();

                $this->redirect(Yii::app()->createUrl('//custom_pages/admin'));
            }
        }


        $this->render('edit', array('page' => $page));
    }

    public function actionDelete()
    {
        $page = CustomPage::model()->findByPk(Yii::app()->request->getParam('id'));

        if ($page !== null) {
            $page->delete();
        }

        $this->redirect(Yii::app()->createUrl('//custom_pages/admin'));
    }

}
