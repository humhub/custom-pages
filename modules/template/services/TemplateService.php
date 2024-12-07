<?php

namespace humhub\modules\custom_pages\modules\template\services;

use humhub\modules\custom_pages\modules\template\elements\AbstractElement;
use humhub\modules\custom_pages\modules\template\models\Template;

class TemplateService
{
    private Template $template;

    /**
     * @var AbstractElement[]
     */
    private array $elements;

    public function __construct(Template $template)
    {
        $this->template = $template;
    }

    private function loadElements()
    {
        foreach ($this->template->elements as $elementModel) {
            $this->elements[] = AbstractElement::create($elementModel);
        }
    }

    /**
     * @return AbstractElement[]
     */
    public function getElements(): array
    {
        return $this->elements;
    }
}
