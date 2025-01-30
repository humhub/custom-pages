<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\models\forms;

use humhub\modules\custom_pages\modules\template\elements\BaseTemplateElementContent;
use humhub\modules\custom_pages\modules\template\models\TemplateElement;

/**
 * This form is used for editing ElementContent entries.
 *
 * @author buddha
 */
class EditElementContentForm extends TemplateElementForm
{
    /**
     * @inheritdoc
     */
    public $scenario = 'edit';

    public ?BaseTemplateElementContent $elementContent = null;

    public function setScenario($value)
    {
        parent::setScenario($value);
        if ($this->content != null) {
            $this->content->scenario = $value;
        }
    }

    /**
     * Sets the initial form data as $elementContent and $element.
     *
     * @param int $elementId
     * @param int $elementContentId
     * @param int $templateInstanceId
     */
    public function setElementData($elementId = null, $elementContentId = null, $templateInstanceId = null)
    {
        $this->elementContent = $elementContentId ? BaseTemplateElementContent::findOne(['id' => $elementContentId]) : null;
        if ($this->elementContent) {
            $this->element = $this->elementContent->element;
        } else {
            $this->element = TemplateElement::findOne(['id' => $elementId]);
            $this->elementContent = $this->element->getDefaultContent(true);
        }

        // If no content was found we either copy the default content or create a empty dummy instance otherwise just set our current content instance.
        if ($this->elementContent->isDefault()) {
            $this->content = $this->elementContent->copy();
            $this->content->template_instance_id = $templateInstanceId;
        } else {
            $this->content = $this->elementContent->getInstance();
        }
    }

    public function save(): bool
    {
        return $this->content->validate() && $this->content->save();
    }
}
