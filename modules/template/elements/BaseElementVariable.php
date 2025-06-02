<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\elements;

use humhub\modules\custom_pages\modules\template\interfaces\TemplateElementContentIterable;

class BaseElementVariable
{
    private array $options = [];

    private BaseElementContent $elementContent;

    public function __construct(BaseElementContent $elementContent, string $mode = 'edit')
    {
        $this->elementContent = $elementContent;
        $this->options['mode'] = $mode;
    }

    public function getLabel()
    {
        return $this->elementContent->getLabel();
    }

    public function isEditMode(): bool
    {
        return isset($this->options['mode']) && $this->options['mode'] === 'edit';
    }

    public function getEmpty()
    {
        return $this->elementContent->isEmpty();
    }

    public function getContent()
    {
        return $this->elementContent;
    }

    public function render(): string
    {
        $options = $this->options;

        if ($this->isEditMode()) {
            $options = array_merge([
                'element_id' => $this->elementContent->element_id,
                'element_content_id' => $this->elementContent->id,
                'element_name' => $this->elementContent->element->name,
                'element_title' => $this->elementContent->element->getTitle(),
                'empty' => $this->elementContent->isEmpty(),
                'default' => $this->elementContent->isDefault(),
            ], $options);
        }

        try {
            if (!$this->elementContent->isEmpty()) {
                return $this->elementContent->render($options);
            }
        } catch (\Exception $e) {
            return strval($e);
        }

        return '';
    }

    public function __toString()
    {
        // Note that the editMode can be set to $this->options in this case
        return $this->render();
    }

    public function items(): iterable
    {
        try {
            yield from $this->elementContent instanceof TemplateElementContentIterable ? $this->elementContent->getItems() : [];
        } catch (\Exception $e) {
            yield from [];
        }
    }

    /**
     * Get a profile field
     *
     * @param string|null $field Field name or NULL to get default field
     * @return string
     */
    public function profile(string $field = null): string
    {
        return $this->elementContent instanceof UserElement ? $this->elementContent->getProfileField($field) : '';
    }
}
