<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\services;

use humhub\modules\custom_pages\modules\template\models\Template;
use humhub\modules\custom_pages\modules\template\models\TemplateInstance;
use humhub\modules\file\models\FileContent;
use Yii;
use yii\db\ActiveRecord;

class ImportInstanceService
{
    private TemplateInstance $instance;
    private array $errors = [];

    public function __construct(TemplateInstance $instance)
    {
        $this->instance = $instance;
    }

    public static function instance(TemplateInstance $instance): self
    {
        return new self($instance);
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
        if (empty($data['templates']) || !is_array($data['templates'])) {
            $this->addError(Yii::t('CustomPagesModule.template', 'Wrong file structure without templates!'));
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
        if (!$this->importTemplateInstance($data)) {
            return false;
        }

        if (isset($data['contents']) && is_array($data['contents'])) {
            foreach ($data['contents'] as $content) {
                $this->importElementContent($content);
            }
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

    private function importTemplateInstance(array $data): bool
    {
        return true;
    }

    private function importElementContent(array $data): bool
    {
        return true;
    }

    private function attachFiles(ActiveRecord $record, array $files)
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
