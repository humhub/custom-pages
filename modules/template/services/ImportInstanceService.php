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
use humhub\modules\file\models\FileContent;
use Yii;
use yii\db\ActiveRecord;

class ImportInstanceService
{
    private TemplateInstance $instance;
    private ?TemplateElement $element = null;
    private array $errors = [];

    public function __construct(TemplateInstance $instance, ?TemplateElement $element = null)
    {
        $this->instance = $instance;
        $this->element = $element;
    }

    public function addError(string $error): void
    {
        $this->errors[] = $error;
    }

    public function hasErrors(): bool
    {
        return $this->getErrors() !== [];
    }

    public function getErrors(): array
    {
        return $this->errors;
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

        if (!$this->validateCompatibility($data)) {
            return false;
        }

        return $this->runImport($data);
    }

    private function validateCompatibility(array $data): bool
    {
        if (empty($data['template'])) {
            $this->addError(Yii::t('CustomPagesModule.template', 'Main template is not defined for importing!'));
            return false;
        }

        if (empty($data['templates']) || !is_array($data['templates'])) {
            $this->addError(Yii::t('CustomPagesModule.template', 'Wrong import file structure without templates!'));
            return false;
        }

        // Check allowed templates
        $mainTemplateIsAllowed = false;
        if ($this->instance->isPage() && !($this->element instanceof TemplateElement)) {
            $mainTemplateIsAllowed = $this->instance->template->name === $data['template'];
        } else {
            if (!($this->element instanceof TemplateElement) ||
                $this->element->content_type !== ContainerElement::class) {
                $this->addError(Yii::t('CustomPagesModule.template', 'Wrong selected container for importing!'));
                return false;
            }
            /* @var ContainerElement $content */
            $content = $this->element->getDefaultContent(true);
            $mainTemplateIsAllowed = !isset($content->definition->templates) ||
                !is_array($content->definition->templates) ||
                !in_array($data['template'], $content->definition->templates);
        }

        if (!$mainTemplateIsAllowed) {
            $this->addError(Yii::t('CustomPagesModule.template', 'Template {name} is not allowed for the instance!', [
                'name' => '"' . $data['template'] . '"',
            ]));
            return false;
        }

        $nonExistentTemplates = [];
        $differentSourceTemplates = [];
        $differentCountElements = [];
        $incompatibleElements = [];
        foreach ($data['templates'] as $jsonTemplateName => $jsonTemplate) {
            $template = Template::findOne(['name' => $jsonTemplateName]);
            if (!$template) {
                $nonExistentTemplates[] = $jsonTemplateName;
                continue;
            }
            if ($template->source !== $jsonTemplate['source']) {
                $differentSourceTemplates[] = $jsonTemplateName;
                continue;
            }
            if (isset($jsonTemplate['elements']) && is_array($jsonTemplate['elements'])) {
                $systemElements = $template->elements;
                if (count($systemElements) !== count($jsonTemplate['elements'])) {
                    $differentCountElements[] = $jsonTemplateName;
                    continue;
                }
                foreach ($systemElements as $systemElement) {
                    foreach ($jsonTemplate['elements'] as $jsonElementName => $jsonElementType) {
                        if ($jsonElementName === $systemElement->name && $jsonElementType !== $systemElement->content_type) {
                            $incompatibleElements[$jsonTemplateName][] = $jsonElementName;
                        }
                    }
                }
            }
        }

        $hasError = false;

        if ($nonExistentTemplates !== []) {
            $this->addError(Yii::t('CustomPagesModule.template', 'Templates {names} don\'t exist in system!', [
                'names' => '"' . implode('", "', $nonExistentTemplates) . '"',
            ]));
            $hasError = true;
        }

        if ($differentSourceTemplates !== []) {
            $this->addError(Yii::t('CustomPagesModule.template', 'Sources of the templates {names} don\'t match with system versions!', [
                'names' => '"' . implode('", "', $differentSourceTemplates) . '"',
            ]));
            $hasError = true;
        }

        if ($differentCountElements !== []) {
            $this->addError(Yii::t('CustomPagesModule.template', 'Templates {names} have different number of elements!', [
                'names' => '"' . implode('", "', $differentCountElements) . '"',
            ]));
            $hasError = true;
        }

        foreach ($incompatibleElements as $jsonTemplateName => $jsonElementNames) {
            $this->addError(Yii::t('CustomPagesModule.template', 'Template {name} has incompatible elements {elements}!', [
                'name' => '"' . $jsonTemplateName . '"',
                'elements' => '"' . implode('", "', $jsonElementNames) . '"',
            ]));
            $hasError = true;
        }

        return !$hasError;
    }

    public function runImport(array $data): bool
    {
        if (isset($data['elements']) && is_array($data['elements'])) {
            $this->importElements($this->instance, $data['elements']);
        }

        return !$this->hasErrors();
    }

    private function saveRecord(ActiveRecord $record): ?ActiveRecord
    {
        if ($record->validate() && $record->save()) {
            return $record;
        }

        $this->addError(implode(' ', $record->getErrorSummary(true)));
        return null;
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

        $content->dyn_attributes = $data['dyn_attributes'];

        if (!$content->save()) {
            $this->addError(Yii::t('CustomPagesModule.template', 'Cannot import element {element} of the template {template}!', [
                'element' => '"' . $element->name . '"',
                'template' => '"' . $templateInstance->template->name . '"',
            ]));
            return;
        }

        if (isset($data['attachedFiles']) && is_array($data['attachedFiles'])) {
            $this->attachFiles($content, $data['attachedFiles']);
        }

        if ($content instanceof ContainerElement) {
            foreach ($content->items as $oldItem) {
                $oldItem->delete();
            }

            if (isset($data['items']) && is_array($data['items'])) {
                foreach ($data['items'] as $i => $itemData) {
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
                    if (!$item->save()) {
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

    private function attachFiles(ActiveRecord $record, array $files): void
    {
        $updateRecord = false;

        $newFiles = [];
        foreach ($files as $fileData) {
            $file = new FileContent();
            foreach ($fileData as $attribute => $value) {
                if (in_array($attribute, ['guid', 'object_model', 'object_id']) ||
                    !$file->hasAttribute($attribute)) {
                    continue;
                }
                if ($attribute === 'base64Content') {
                    $file->newFileContent = base64_decode($value);
                } else {
                    $file->$attribute = $value;
                }
            }
            if ($file->save()) {
                $newGuid = $file->guid;
                $newFiles[] = $file;
            } else {
                $newGuid = '';
            }

            foreach ($record->attributes as $attribute => $value) {
                if ($value === $fileData['guid']) {
                    $record->$attribute = $newGuid;
                    $updateRecord = true;
                }
            }
        }

        if ($newFiles !== []) {
            $record->fileManager->attach($newFiles);
        }

        if ($updateRecord) {
            $record->save();
        }
    }
}
