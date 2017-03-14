<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\models\forms;

use Yii;
use humhub\modules\custom_pages\modules\template\models\TemplateElement;
use humhub\modules\custom_pages\modules\template\models\OwnerContent;

/**
 * This form is used for editing OwnerContent entries.
 * 
 * @author buddha
 */
class EditOwnerContentForm extends TemplateElementForm
{
    /**
     * @inheritdoc
     * @var string
     */
    public $scenario = 'edit';
    
    /**
     * The content owner instance.
     * @var \humhub\modules\custom_pages\modules\template\models\TemplateContentOwner 
     */
    public $owner;
    
    /**
     * The OwnerContent instance to be edited, before successfully saving the form
     * this variable is possibly assigned with a default or empty OwnerContent instance.
     * 
     * After saving the form the $ownerContent will be replaced with the actual OwnerContent
     * owned by $owner.
     * 
     * @var \humhub\modules\custom_pages\modules\template\models\OwnerContent 
     */
    public $ownerContent;
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'use_default' => Yii::t('CustomPagesModule.modules_template_models_forms_EditOwnerContentForm', 'Use default content')
        ];
    }
    
    public function setScenario($value)
    {
        parent::setScenario($value);
        if($this->element != null) {
            $this->element->scenario = $value;
        }
        if($this->content != null) {
            $this->content->scenario = $value;
        }
    }

    /**
     * Sets the initial form data as $owner, $element and $content.
     * 
     * @param string $ownerModel owner model classname.
     * @param integer $ownerId owner model id
     * @param string $elementName the placeholder name which should be filled with the edited content.
     */
    public function setElementData($ownerModel, $ownerId, $elementName)
    {
        $this->owner = call_user_func($ownerModel."::findOne", ['id' => $ownerId]);
        $this->element = TemplateElement::findOne(['template_id' => $this->owner->getTemplateId(), 'name' => $elementName]);

        // Search for current ownerContent for this placeholder/owner
        $this->ownerContent = OwnerContent::findByOwner($ownerModel, $ownerId, $elementName)->one();

        // If no content was found we either copy the default content or create a empty dummy instance otherwise just set our current content instance.
        if ($this->ownerContent == null) {
            $this->ownerContent = $this->element->getDefaultContent(true);
            $this->content = ($this->ownerContent->isEmpty()) ? $this->ownerContent->getInstance(true) : $this->ownerContent->copyContent();
        } else {
            $this->content = $this->ownerContent->getInstance();
        }
        
        $this->use_default = $this->ownerContent->use_default;
    }

    /**
     * Validates and saves the content instance.
     * 
     * If the current $ownerContent instance is a default OwnerContent we create a new OwnerContent instance for
     * the given owner and overwrite the current $ownerContent variable.
     * 
     * @return boolean
     */
    public function save()
    {
        if ($this->validate()) {
            if($this->ownerContent->isDefault()) {
                $this->ownerContent = $this->element->saveInstance($this->owner, $this->content, $this->use_default);
            } else {
                $this->content->save();
                $this->ownerContent->use_default = $this->use_default;
                $this->ownerContent->save();
            }
            
            return $this->element->save(false);
        } else {
            return false;
        }
    }
    
    /**
     * @inheritdoc
     * @return boolean
     */
    public function validate($attributeNames = null, $clearErrors = true)
    {
        return $this->content->validate();
    }
}
