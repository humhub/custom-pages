<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\models\forms;

use Yii;
use humhub\modules\custom_pages\modules\template\models\TemplateElement;

/**
 * Form model used to edit TemplateElements and Template default content.
 *
 * @author buddha
 */
class EditElementForm extends TemplateElementForm
{   
    public $defaultOwnerContent;
    
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'use_default' => Yii::t('CustomPagesModule.modules_template_models_forms_EditElementForm', 'Use empty content')
        ];
    }
    
    /**
     * Initializes the form data.
     * 
     * @param type $templateId
     * @param type $type
     */
    public function setElementId($elementId)
    {
        $this->element = TemplateElement::findOne(['id' => $elementId]);
        $this->defaultOwnerContent = $this->element->getDefaultContent(true);
        $this->use_default = $this->defaultOwnerContent->use_default;
        $this->content = $this->defaultOwnerContent->getInstance(true);
    }    
    
    public function save()
    {
        if($this->validate()) {
            $this->element->save();
            
            // Try saving the default content if
            if($this->content->save()) {
                $this->defaultOwnerContent->setContent($this->content);
                $this->defaultOwnerContent->use_default = $this->use_default;
                $this->defaultOwnerContent->save();
            }
            return true;
        } 
        
        return false;
    }
}
