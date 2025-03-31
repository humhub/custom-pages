<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\services;

use humhub\modules\custom_pages\modules\template\elements\BaseElementContent;
use humhub\modules\custom_pages\modules\template\models\Template;
use humhub\modules\custom_pages\modules\template\models\TemplateElement;
use humhub\modules\file\models\FileContent;
use yii\base\InvalidConfigException;
use Yii;
use yii\db\ActiveRecord;

class ImportService
{
    private string $type;
    private string $filePath;
    private array $errors = [];
    public ?Template $template = null;

    public function __construct(string $type, string $filePath)
    {
        $this->type = $type;
        $this->filePath = $filePath;
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

    public function run(): bool
    {
        if (!file_exists($this->filePath)) {
            $this->addError('The import file is not found!');
            return false;
        }

        try {
            $data = json_decode(file_get_contents($this->filePath), true);
        } catch (\Exception $e) {
            $this->addError('The import file is not readable! Error: ' . $e->getMessage());
            return false;
        }

        if (empty($data['name'])) {
            $this->addError(Yii::t('CustomPagesModule.template', 'Wrong import data!'));
            return false;
        }

        if (isset($data['type']) && $data['type'] !== $this->type) {
            $this->addError(Yii::t('CustomPagesModule.template', 'The template can be imported only as {type}!', [
                'type' => Template::getTypeTitle($data['type']),
            ]));
            return false;
        }

        if (!$this->importTemplate($data)) {
            return false;
        }

        $importedElementNames = [];
        if (isset($data['elements']) && is_array($data['elements'])) {
            foreach ($data['elements'] as $element) {
                if ($element = $this->importElement($element)) {
                    $importedElementNames[] = $element->name;
                }
            }
        }

        // Delete old template elements that are not found in the new template
        $oldElements = TemplateElement::find()
            ->where(['template_id' => $this->template->id])
            ->andWhere(['NOT IN', 'name', $importedElementNames]);
        foreach ($oldElements->each() as $oldElement) {
            $oldElement->delete();
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

    private function importTemplate(array $data): bool
    {
        $template = Template::findOne(['name' => $data['name']]) ?? new Template();

        $template->type = $this->type;
        $template->name = $data['name'];
        $template->engine = $data['engine'] ?? 'twig';
        $template->description = $data['description'] ?? '';
        $template->source = $data['source'] ?? '';
        $template->allow_for_spaces = $data['allow_for_spaces'] ?? false;

        $this->template = $this->saveRecord($template);

        return $this->template instanceof Template;
    }

    private function importElement(array $data): ?TemplateElement
    {
        $element = TemplateElement::findOne([
            'template_id' => $this->template->id,
            'name' => $data['name'],
        ]);

        if ($element) {
            $element->setScenario(TemplateElement::SCENARIO_EDIT_ADMIN);
        } else {
            $element = new TemplateElement();
            $element->setScenario(TemplateElement::SCENARIO_CREATE);
        }

        $element->template_id = $this->template->id;
        $element->name = $data['name'] ?? '';
        $element->content_type = $data['content_type'] ?? '';
        $element->title = $data['title'] ?? '';
        $element->dyn_attributes = $data['dyn_attributes'] ?? '';

        if (!class_exists($element->content_type)) {
            $this->addError('Element content class "' . $element->content_type . '" does not exist!');
            return null;
        }

        if (!isset($data['elementContent'])) {
            $this->addError('Missed content for element with name "' . $data['name'] . '"!');
            return null;
        }

        if (!$this->saveRecord($element)) {
            return null;
        }

        $data['elementContent']['element_id'] = $element->id;

        $this->importElementContent($element, $data['elementContent']);

        return $element;
    }

    private function importElementContent(TemplateElement $element, array $data): ?ActiveRecord
    {
        $elementContent = $element->getDefaultContent(true);

        foreach ($data as $name => $value) {
            if ($name === 'id' || ($name !== 'dyn_attributes' && is_array($value))) {
                continue;
            }
            $elementContent->$name = $value;
        }

        $elementContent = $this->saveRecord($elementContent);
        if (!$elementContent) {
            return null;
        }

        if (isset($data['attachedFiles']) && is_array($data['attachedFiles'])) {
            $this->attachFiles($elementContent, $data['attachedFiles']);
        }

        return $elementContent;
    }

    private function attachFiles(ActiveRecord $record, array $files)
    {
        $recordAttributes = $record->attributes;
        $updateRecord = false;

        $newFiles = [];
        foreach ($files as $fileData) {
            $file = new FileContent();
            foreach ($fileData as $attribute => $value) {
                if (in_array($attribute, ['guid', 'object_model', 'object_id'])) {
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

            foreach ($recordAttributes as $attribute => $value) {
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
