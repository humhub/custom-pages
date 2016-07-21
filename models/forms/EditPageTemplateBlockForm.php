<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\models\forms;

use Yii;
use humhub\modules\custom_pages\models\TemplateBlock;

/**
 * Description of UserGroupForm
 *
 * @author buddha
 */
class EditPageTemplateBlockForm extends TemplateBlockForm
{
    public $blockName;
    public $pageTemplateId;
    
    public function rules()
    {
        return [
            [['pageTemplateId', 'blockName'], 'required']
        ];
    }
    
    public function setPageTemplateBlock($id, $blockName)
    {
        $this->pageTemplateId = $id;
        $this->blockName = $blockName;
        $this->templateId = null;
        
        $pageTemplate = \humhub\modules\custom_pages\models\PageTemplate::findOne(['id' => $id]);
        $defaultBlock = TemplateBlock::findOne(['template_id' => $pageTemplate->template_id]);
        $this->type = $defaultBlock->type;
        
        $this->templateBlock = TemplateBlock::findOne(['page_template_id' => $id, 'name' => $blockName]);
        
        if($this->templateBlock == null) {
            $this->templateBlock = new TemplateBlock();
            $this->templateBlock->name = $blockName;
            $this->templateBlock->type = $this->type;
            $this->templateBlock->page_template_id = $id;
            $this->templateContent = Yii::createObject($this->type);
        } else if($this->templateBlock->hasContent()){
            $this->templateContent = $this->templateBlock->content;
        } else {
            $this->templateContent = Yii::createObject($this->type);
        }
    }
    
}
