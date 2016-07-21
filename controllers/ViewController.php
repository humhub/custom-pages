<?php

namespace humhub\modules\custom_pages\controllers;

use Yii;
use yii\web\HttpException;
use humhub\components\Controller;
use humhub\modules\custom_pages\models\Page;

/**
 * Description of ViewController
 *
 * @author luke
 */
class ViewController extends Controller
{

    public function actionIndex()
    {
        $page = Page::findOne(['id' => Yii::$app->request->get('id')]);

        if ($page === null) {
            throw new HttpException('404', 'Could not find requested page');
        }

        if ($page->admin_only == 1 && !Yii::$app->user->isAdmin()) {
            throw new HttpException(403, 'Access denied!');
        }

        if ($page->navigation_class == Page::NAV_CLASS_ACCOUNTNAV) {
            $this->subLayout = "@humhub/modules/user/views/account/_layout";
        }

        if ($page->type == Page::TYPE_HTML) {
            return $this->render('html', array('html' => $page->content, 'title' => $page->title));
        } elseif ($page->type == Page::TYPE_IFRAME) {
            return $this->render('iframe', array('url' => $page->content, 'navigationClass' => $page->navigation_class));
        } elseif ($page->type == Page::TYPE_LINK) {
            return $this->redirect($page->content);
        } elseif ($page->type == Page::TYPE_TEMPLATE) {
            return $this->viewTemplatePage($page);
        } elseif ($page->type == Page::TYPE_MARKDOWN) {
            return $this->render('markdown', array(
                'md' => $page->content,
                'navigationClass' => $page->navigation_class,
                'title' => $page->title
            ));
        } else {
            throw new HttpException('500', 'Invalid page type!');
        }
    }
    
     public function viewTemplatePage($page)
    {
        $pageTemplate = \humhub\modules\custom_pages\models\PageTemplate::findOne(['page_id' => $page->id]);
        $template = $pageTemplate->template;
        
        $loader = new \humhub\modules\custom_pages\lib\twig\DatabaseTwigLoader();
        $twig = new \Twig_Environment($loader, ['autoescape' => false]);
        
        $blocks = $pageTemplate->getBlocks();
        $content = [];
        foreach($blocks as $block) {
            $content[$block->name] = $block->render();
        }

        $html = $twig->render($template->name ,$content);
        return $this->render('template', ['pageTemplate' => $pageTemplate, 'html' => $html]);
    }

}
