<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\models\forms;

use Yii;
use humhub\modules\custom_pages\modules\template\models\Template;

/**
 * Description of UserGroupForm
 *
 * @author buddha
 */
class EditMultipleElementsForm extends \yii\base\Model
{

    public $isNewRecord = false;
    public $editDefault = true;
    public $owner;
    public $template;
    public $contentMap = [];
    public $ownerContentMap = [];
    public $scenario = 'edit';

    public function setOwnerTemplateId($templateId)
    {
        $this->setTemplate($templateId);
        $this->owner = $this->template;
    }

    // Todo: is the templateId even needed since the woner should have contain a templateid ...
    public function setOwner($ownerModel, $ownerId, $templateId = null)
    {
        if(!is_string($ownerModel)) {
            $templateId = $ownerId;
            $this->owner = $ownerModel;
        } else {
            $this->owner = call_user_func($ownerModel."::findOne", ['id' => $ownerId]);
        }
        
        $this->setTemplate($templateId);
    }

    protected function setTemplate($templateId)
    {
        $this->template = Template::find()->where(['custom_pages_template.id' => $templateId])->joinWith('elements')->one();
        $this->prepareContentInstances();
    }

    public function prepareContentInstances()
    {
        $ownerContentArr = $this->template->getContentElements($this->owner);

        foreach ($ownerContentArr as $ownerContent) {            
            $contentItem = new ContentFormItem([
                'ownerContent' => $ownerContent, 
                'element' => $this->getElement($ownerContent->element_name),
                'editDefault' => $this->editDefault,
                'scenario' => $this->scenario]);
            $this->contentMap[$contentItem->key] = $contentItem;
        }
    }
    
    public function getElement($name)
    {
        foreach ($this->template->elements as $element) {
            if ($name === $element->name) {
                return $element;
            }
        }
    }

    public function load($data, $formName = NULL)
    {
        // This prevents items without elements from beeing rejected
        if(parent::load($data) && empty($this->contentMap)) {
            return true;
        }
        
        $result = false;
        
        // If one of the content was loaded we expect a successful form submit
        foreach ($this->contentMap as $key => $contentItem) {
            if($contentItem->load($data)) {
                $result = true;
            }
        }
        
        return $result;
    }

    public function validate($attributeNames = null, $clearErrors = true)
    {
        // Default content is not mandatory
        if($this->scenario === 'edit-admin') {
            return true;
        }
        
        //Todo: implement multiedit content validation. Skip validation if no values are set. 
        /*$result = true;
        foreach ($this->contentMap as $key => $contentItem) {
            if(!$contentItem->validate()) {
                $result = false;
            }
        }*/
        
        return true;
    }

    public function save()
    {
        if (!$this->validate()) {
            return false;
        }
        
        $transaction = Template::getDb()->beginTransaction();
        
        try {
            foreach ($this->contentMap as $key => $contentItem) {
                $contentItem->save($this->owner);
            }
            $transaction->commit();
        } catch(\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
        
        return true;
    }
}
