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
 * Form model used to add new TemplateElement instances to a Template.
 *
 * @author buddha
 */
class AddElementForm extends TemplateElementForm
{

    /**
     * Owner TemplateId of the new element definition.
     * 
     * @var integer
     */
    public $templateId;

    /**
     * Content type definition for this element.
     * 
     * @var string 
     */
    public $type;
    
    /**
     * Default scenario
     * @var type 
     */
    public $scenario = 'create';

    /**
     * @inheritdocs
     */
    public function init()
    {
        $this->element = new TemplateElement(['scenario' => 'create']);
    }

    /**
     * @inheritdocs
     */
    public function rules()
    {
        return [
            [['templateId', 'type'], 'required']
        ];
    }

    /**
     * Initializes the form data.
     * 
     * Todo: rename because of definition...
     * 
     * @param type $templateId
     * @param type $type
     */
    public function setElementDefinition($templateId, $type)
    {
        $this->templateId = $templateId;
        $this->type = $type;

        $this->content = Yii::createObject($type);
        $this->element->content_type = $type;
        $this->element->template_id = $templateId;
    }

    /**
     * Validates and saves the TemplateElement instance and appended files.
     * 
     * @return boolean
     */
    public function save()
    {
        if ($this->validate()) {
            // Try saving the default content if
            $this->element->saveAsDefaultContent($this->content);
            return $this->element->save(false);
        } else {
            return false;
        }
    }

}
