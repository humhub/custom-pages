<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\models\forms;

use humhub\modules\custom_pages\modules\template\elements\BaseTemplateElementContent;
use humhub\modules\custom_pages\modules\template\models\TemplateElement;

/**
 * Form model used to edit TemplateElements and Template default content.
 *
 * @author buddha
 */
class EditElementForm extends TemplateElementForm
{
    /**
     * Initializes the form data.
     *
     * @param int $elementId
     */
    public function setElementId($elementId)
    {
        $this->element = TemplateElement::findOne(['id' => $elementId]);
        $this->content = $this->element->getDefaultContent(true);
    }

    public function save()
    {
        if ($this->validate() && $this->element->save()) {
            if ($this->content instanceof BaseTemplateElementContent) {
                $this->content->element_id = $this->element->id;
            }
            return $this->content->save();
        }

        return false;
    }
}
