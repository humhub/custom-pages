<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\models\forms;

use Yii;
use humhub\modules\custom_pages\models\TemplateBlock;
use humhub\modules\custom_pages\models\Template;

/**
 * Description of UserGroupForm
 *
 * @author buddha
 */
class TemplateBlockForm extends TemplateBlock
{
    public $type;
    public $action;
    public $templateId;
    public $templateContent;
    public $templateBlock;
    public $isNewRecord = true;
    
    
    public function init()
    {
        $this->templateBlock = new TemplateBlock();
        $this->isNewRecord = true;
    }
    
    public function setContentType($type)
    {
        $this->templateContent = Yii::createObject($type);
        $this->type = $type;
        $this->templateBlock->type = $type;
    }
    
    public function setTemplateId($id)
    {
        $this->templateId = $id;
        $this->templateBlock->template_id = $id;
        $this->templateBlock->page_template_id = null;
    }
    
    public function setTemplateBlock($block)
    {
        if($block != null) {
            $this->templateBlock = $block;
            $this->isNewRecord = $block->isNewRecord;
        }
    }
    
    public function load($data, $formName = null)
    {
        $this->templateContent->load($data, $formName);
        return $this->templateBlock->load($data, $formName);
    }
    
    public function validate()
    {
        return parent::validate() && $this->templateBlock->validate();
    }
    
    public function getName()
    {
        return $this->templateBlock->name;
    }
    
    public function contentEditForm($form)
    {
        return $this->getContentIntance()->renderEditForm($form, $this);
    }
    
    public function getTypeLabel()
    {
        return $this->getContentIntance()->getLabel();
    }
    
    private function getContentIntance()
    {
        return ($this->templateContent == null) ?  Yii::createObject($this->type)
                : Yii::createObject($this->type);

    }
    
    private function getTemplate()
    {
        if($this->templateId != null) {
            return Template::findOne(['id' => $this->templateId]);
        }
    }

    /**
     * Aligns the given group selection with the db
     * @return boolean
     */
    public function save($runValidation = true, $attributeNames = NULL)
    {
        $result = $this->templateBlock->save();
        
        //TODO: remove templateContent if templateblock could not be saved ?
        if($result && $this->templateContent->validate() && $this->templateContent->save()) {
            $this->templateBlock->template_content_id = $this->templateContent->id;
        }
        return $result;
    }
}
