<?php

namespace module\custom_pages\controllers;

use Yii;
use yii\web\HttpException;
use humhub\components\Controller;
use module\custom_pages\models\CustomPage;

/**
 * Description of ViewController
 *
 * @author luke
 */
class ViewController extends Controller
{

    public function actionIndex()
    {
        $page = CustomPage::findOne(['id' => Yii::$app->request->get('id')]);

        if ($page === null) {
            throw new HttpException('404', 'Could not find requested page');
        }

        if ($page->admin_only == 1 && !Yii::$app->user->isAdmin()) {
            throw new HttpException(403, 'Access denied!');
        }

        if ($page->navigation_class == CustomPage::NAV_CLASS_ACCOUNTNAV) {
            $this->subLayout = "@humhub/modules/user/views/account/_layout";
        }

        if ($page->type == CustomPage::TYPE_HTML) {
            return $this->render('html', array('html' => $page->content));
        } elseif ($page->type == CustomPage::TYPE_IFRAME) {
            return $this->render('iframe', array('url' => $page->content, 'navigationClass' => $page->navigation_class));
        } elseif ($page->type == CustomPage::TYPE_LINK) {
            return $this->redirect($page->content);
        } elseif ($page->type == CustomPage::TYPE_MARKDOWN) {
            return $this->render('markdown', array('md' => $page->content, 'navigationClass' => $page->navigation_class));
        } else {
            throw new HttpException('500', 'Invalid page type!');
        }
    }

}
