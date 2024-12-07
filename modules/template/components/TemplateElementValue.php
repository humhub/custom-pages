<?php

namespace humhub\modules\custom_pages\modules\template\components;

use humhub\modules\custom_pages\modules\template\elements\AbstractElement;

/**
 * Standard Object which is provided as a variable via Twig.
 * This can be extended for more complex variables.
 */
class TemplateElementValue
{
    private AbstractElement $element;

    public $value = '';

    public function __construct(AbstractElement $element, string $value)
    {
        $this->element = $element;
    }

    public function __toString()
    {
        return $this->value;
    }

}
