<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace humhub\modules\custom_pages\components;

use Yii;
use yii\base\Behavior;
use humhub\modules\custom_pages\modules\template\models\TemplateInstance;
use humhub\modules\custom_pages\modules\template\models\TemplatePagePermission;
use humhub\modules\custom_pages\modules\template\components\TemplateCache;

/**
 * Description of AbstractContainer
 *
 * @author buddha
 */
class TemplateViewBehavior extends Behavior
{
    
    public $_canEdit;
    
    /**
     * 
     * @param \humhub\modules\custom_pages\models\CustomContentContainer $page
     * @return string rendered template page
     * @throws \yii\web\HttpException in case the page is protected from non admin access
     */
    public function viewTemplatePage(\humhub\modules\custom_pages\models\CustomContentContainer $page)
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
    
    public function renderTemplate($page, $editMode = null)
    {
        $templateInstance = TemplateInstance::findOne(['object_model' => $page->className() ,'object_id' => $page->id]);
        
        $canEdit = $this->isCanEdit();
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
    
    public function isCanEdit() {
        if($this->_canEdit == null) {
            $this->_canEdit = TemplatePagePermission::canEdit();
        }
        return $this->_canEdit;
    }
}
