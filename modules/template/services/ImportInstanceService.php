<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\services;

use humhub\modules\custom_pages\modules\template\elements\BaseElementContent;
use humhub\modules\custom_pages\modules\template\elements\ContainerElement;
use humhub\modules\custom_pages\modules\template\elements\ContainerItem;
use humhub\modules\custom_pages\modules\template\models\Template;
use humhub\modules\custom_pages\modules\template\models\TemplateElement;
use humhub\modules\custom_pages\modules\template\models\TemplateInstance;
use Yii;

/**
 * Service to import Template Instance (Custom Page or Container Item)
 */
class ImportInstanceService extends BaseImportService
{
    private TemplateInstance $instance;
    private ?TemplateElement $element = null;

    public function __construct(TemplateInstance $instance, ?TemplateElement $element = null)
    {
        $this->instance = $instance;
        $this->element = $element;
    }

    public function importFromFile(string $path): bool
    {
        if (!file_exists($path)) {
            $this->addError('The import file is not found!');
            return false;
        }

        try {
            $data = json_decode(file_get_contents($path), true);
        } catch (\Exception $e) {
            $this->addError('The import file is not readable! Error: ' . $e->getMessage());
            return false;
        }

        if (!$this->validateCompatibility($this->element, $data)) {
            return false;
        }

        return $this->run($data);
    }

    private function validateCompatibility(?TemplateElement $element, array $data): bool
    {
        if (empty($data['template'])) {
            $this->addError(Yii::t('CustomPagesModule.template', 'Template is not defined!'));
            return false;
        }

        $template = Template::findOne(['name' => $data['template']]);
        if (!$template) {
            $this->addError(Yii::t('CustomPagesModule.template', 'Template "{name}" is not found in system!', [
                'name' => $data['template'],
            ]));
            return false;
        }

        if ($element instanceof TemplateElement) {
            if ($element->content_type !== ContainerElement::class) {
                $this->addError(Yii::t('CustomPagesModule.template', 'Element "{name}" cannot be used as container!', [
                    'name' => $element->name,
                ]));
                return false;
            }
            $allowedTemplates = $element->getDefaultContent(true)?->definition?->templates;
            $templateIsAllowed = empty($allowedTemplates) || in_array($data['template'], $allowedTemplates);
        } else {
            $templateIsAllowed = $this->instance->template->name === $data['template'];
        }

        if (!$templateIsAllowed) {
            $this->addError(Yii::t('CustomPagesModule.template', 'Template "{name}" is not allowed for the selected instance!', [
                'name' => $data['template'],
            ]));
            return false;
        }

        if (isset($data['elements']) && is_array($data['elements'])) {
            $systemElements = $template->elements;

            if (count($systemElements) !== count($data['elements'])) {
                $this->addError(Yii::t('CustomPagesModule.template', 'Mismatch number of elements for the template "{name}"!', [
                    'name' => $data['template'],
                ]));
                return false;
            }

            $incompatibleElements = [];
            foreach ($systemElements as $systemElement) {
                $jsonElement = $data['elements'][$systemElement->name] ?? null;
                if ($jsonElement === null ||
                    !isset($jsonElement['__element_type']) ||
                    $jsonElement['__element_type'] !== $systemElement->content_type) {
                    $incompatibleElements[] = $systemElement->name;
                    continue;
                }

                if ($jsonElement['__element_type'] === ContainerElement::class &&
                    isset($jsonElement['__element_items']) &&
                    is_array($jsonElement['__element_items'])) {
                    foreach ($jsonElement['__element_items'] as $elementItem) {
                        $this->validateCompatibility($systemElement, $elementItem);
                    }
                }
            }

            if ($incompatibleElements !== []) {
                $this->addError(Yii::t('CustomPagesModule.template', 'Template "{name}" has incompatible elements {elements}!', [
                    'name' => $data['template'],
                    'elements' => '"' . implode('", "', $incompatibleElements) . '"',
                ]));
                return false;
            }
        }

        return !$this->hasErrors();
    }

    /**
     * @inheritdoc
     */
    public function run(array $data): bool
    {
        if (isset($data['elements']) && is_array($data['elements'])) {
            if ($this->element instanceof TemplateElement) {
                // Import Container Item
                $this->importElement($this->instance, $this->element, [
                    '__element_items' => [$data],
                ]);
            } else {
                // Import Custom Page
                $this->importElements($this->instance, $data['elements']);
            }
        }

        return !$this->hasErrors();
    }

    private function importElements(TemplateInstance $templateInstance, array $elements): void
    {
        foreach ($templateInstance->template->elements as $element) {
            if (isset($elements[$element->name])) {
                $this->importElement($templateInstance, $element, $elements[$element->name]);
            }
        }
    }

    private function importElement(TemplateInstance $templateInstance, TemplateElement $element, array $data): void
    {
        $content = BaseElementContent::findOne([
            'element_id' => $element->id,
            'template_instance_id' => $templateInstance->id,
        ]);

        if (!$content) {
            $content = BaseElementContent::createByType($element->content_type);
            $content->element_id = $element->id;
            $content->template_instance_id = $templateInstance->id;
        }

        foreach ($data as $attrName => $attrValue) {
            if ($content->hasDynamicAttribute($attrName)) {
                $content->$attrName = $attrValue;
            }
        }
        $content = $this->saveRecord($content);

        if ($content === null) {
            $this->addError(Yii::t('CustomPagesModule.template', 'Cannot import element {element} of the template {template}!', [
                'element' => '"' . $element->name . '"',
                'template' => '"' . $templateInstance->template->name . '"',
            ]));
            return;
        }

        if (isset($data['__element_files']) && is_array($data['__element_files'])) {
            $this->attachFiles($content, $data['__element_files']);
        }

        if ($content instanceof ContainerElement) {
            if ($this->element === null) {
                // Remove old container items only on import full custom page
                foreach ($content->items as $oldItem) {
                    $oldItem->delete();
                }
            }

            if (isset($data['__element_items']) && is_array($data['__element_items'])) {
                foreach ($data['__element_items'] as $i => $itemData) {
                    if (!$content->canAddItem()) {
                        $this->addError(Yii::t('CustomPagesModule.template', 'Cannot add item {itemNumber} with template {template} because the container doesn\'t allow multiple items!', [
                            'itemNumber' => $i,
                            'template' => $itemData['template'] ?? '',
                        ]));
                        continue;
                    }

                    $item = new ContainerItem();
                    $item->pageId = $templateInstance->page_id;
                    $item->templateId = Template::findOne(['name' => $itemData['template']])->id;
                    $item->element_content_id = $content->id;
                    $item->sort_order = $itemData['sort_order'] ?? 0;
                    $item->title = $itemData['title'] ?? 0;
                    $item = $this->saveRecord($item);

                    if ($item === null) {
                        $this->addError(Yii::t('CustomPagesModule.template', 'Cannot import container item {itemNumber} with template {template}!', [
                            'itemNumber' => $i,
                            'template' => $itemData['template'] ?? '',
                        ]));
                        continue;
                    }

                    if (isset($itemData['elements']) && is_array($itemData['elements'])) {
                        $this->importElements($item->templateInstance, $itemData['elements']);
                    }
                }
            }
        }
    }
}
