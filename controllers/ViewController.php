<?php

namespace humhub\modules\custom_pages\controllers;

use humhub\modules\custom_pages\models\ContentType;
use humhub\modules\custom_pages\models\HtmlType;
use humhub\modules\custom_pages\models\IframeType;
use humhub\modules\custom_pages\models\LinkType;
use humhub\modules\custom_pages\models\MarkdownType;
use humhub\modules\custom_pages\models\PhpType;
use humhub\modules\custom_pages\models\TemplateType;
use Yii;
use yii\base\ViewNotFoundException;
use yii\web\HttpException;
use humhub\components\Controller;
use humhub\modules\custom_pages\models\Page;
use humhub\modules\custom_pages\components\Container;
use humhub\modules\custom_pages\components\TemplateViewBehavior;

/**
 * Controller for viewing Pages.
 *
 * @author buddha
 */
class ViewController extends Controller
{

    public function getAccessRules()
    {
        return [
            ['strict'],
            ['login' => ['edit']]
        ];
    }
    /**
     * @inhritdoc
     */
    public function behaviors()
    {
        $result = parent::behaviors();
        $result [] = ['class' => TemplateViewBehavior::class];
        return $result;
    }
    
      /**
     * Is used to view/render a Page of a certain page content type.
     * 
     * This action expects an page id as request parameter.
     * 
     * @return string
       * @throws HttpException if the page was not found
     */
    public function actionIndex()
    {
        $page = Page::findOne(['id' => Yii::$app->request->get('id')]);

        if ($page === null) {
            throw new HttpException('404', 'Could not find requested page');
        }

        if ($page->admin_only == 1 && !Yii::$app->user->isAdmin()) {
            throw new HttpException(403, 'Access denied!');
        }

        if ($page->hasTarget(Page::NAV_CLASS_ACCOUNTNAV)) {
            $this->subLayout = "@humhub/modules/user/views/account/_layout";
        }
        
        if ($page->hasTarget(Page::NAV_CLASS_DIRECTORY)) {
            $this->subLayout = "@humhub/modules/custom_pages/views/layouts/_directory_layout";
        }
        
        $this->getView()->pageTitle = $page->title;

        switch ($page->type) {
            case HtmlType::ID:
                return $this->render('html', ['page' => $page, 'html' => $page->page_content, 'title' => $page->title]);
            case IframeType::ID:
                return $this->render('iframe', ['page' => $page, 'url' => $page->page_content, 'navigationClass' => $page->getTargetId()]);
            case TemplateType::ID:
                return $this->viewTemplatePage($page);
            case LinkType::ID:
                return $this->redirect($page->page_content);
            case MarkdownType::ID:
                return $this->render('markdown', [
                    'page' => $page,
                    'md' => $page->page_content,
                    'navigationClass' => $page->getTargetId(),
                    'title' => $page->title
                ]);
            case PhpType::ID:
                return $this->render('php', ['page' => $page]);
            default:
                throw new HttpException('500', 'Invalid page type!');
        }
    }

    /**
     * This redirect is needed within some common views shared with container page logic.
     * @return string
     * @throws HttpException
     */
    public function actionView()
    {
        return $this->actionIndex();
    }
    
    /**
     * This redirect is needed within some common views shared with container page logic.
     * @return string
     */
    public function actionEdit($id)
    {
        return $this->redirect(\yii\helpers\Url::to(['/custom_pages/admin/edit', 'id' => $id]));
    }
}
