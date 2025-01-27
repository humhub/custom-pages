<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\models;

use humhub\modules\custom_pages\modules\template\elements\BaseTemplateElementContent;
use humhub\modules\custom_pages\modules\template\elements\ContainerElement;
use humhub\modules\custom_pages\modules\template\elements\UserElement;
use yii\base\Model;

class ElementContentVariable extends Model
{
    public $options = [];

    public ?BaseTemplateElementContent $elementContent = null;

    public function getLabel()
    {
        return $this->elementContent->getLabel();
    }

    public function isEditMode(): bool
    {
        return $this->options['editMode'] ?? false;
    }

    public function getEmptyContent()
    {
        return $this->elementContent->renderEmpty();
    }

    public function getEmpty()
    {
        return $this->elementContent->isEmpty();
    }

    public function getContent()
    {
        return $this->elementContent;
    }

    public function render(bool $editMode = false): string
    {
        if ($editMode) {
            $this->options['editMode'] = true;
        }

        if (isset($this->options['editMode']) && $this->options['editMode']) {
            $options = array_merge([
                'empty' => $this->elementContent->isEmpty(),
                'element_content_id' => $this->elementContent->id,
                'template_instance_id' => $this->elementContent->template_instance_id,
                'element_name' => $this->elementContent->element->name,
                'default' => $this->elementContent->isDefault(),
            ], $this->options);

            $options['template_instance_type'] = TemplateInstance::getTypeById($options['template_instance_id']);

            // We only need the template_id for container content elements
            if ($this->elementContent instanceof ContainerElement) {
                $options['template_id'] = $this->elementContent->templateInstance?->template_id;
            }
        } else {
            $options = $this->options;
        }

        try {
            if (!$this->elementContent->isEmpty()) {
                return $this->elementContent->render($options);
            } elseif ($this->isEditMode()) {
                return $this->elementContent->renderEmpty($options);
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
            yield from $this->elementContent instanceof TemplateContentIterable ? $this->elementContent->getItems() : [];
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
