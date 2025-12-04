<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\elements;

use humhub\modules\custom_pages\modules\template\models\TemplateInstance;

class ContainerParentVariable implements \Stringable
{
    private array $parentElements = [];

    public function __construct(protected TemplateInstance $templateInstance)
    {
        $parentTemplateInstance = $this->templateInstance?->containerItem?->container?->templateInstance;

        if ($parentTemplateInstance instanceof TemplateInstance) {
            $elementContents = $parentTemplateInstance->template->getElementContents($parentTemplateInstance);
            foreach ($elementContents as $elementContent) {
                $this->parentElements[$elementContent->element->name] = $elementContent->getTemplateVariable();
            }
        }
    }

    public function __isset(string $name): bool
    {
        return isset($this->$name) || isset($this->parentElements[$name]);
    }

    public function __get($name)
    {
        return $this->$name ?? $this->parentElements[$name] ?? null;
    }

    public function __toString(): string
    {
        return (string) $this->templateInstance?->containerItem?->container?->element?->title;
    }
}
