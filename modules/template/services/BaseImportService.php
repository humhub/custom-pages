<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\services;

use humhub\modules\custom_pages\modules\template\elements\BaseElementContent;
use humhub\modules\file\models\FileContent;
use yii\db\ActiveRecord;

abstract class BaseImportService
{
    protected array $errors = [];

    /**
     * Run import process from the provided data array
     *
     * @param array $data
     * @return bool
     */
    abstract public function run(array $data): bool;

    protected function addError(string $error): void
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

    protected function saveRecord(ActiveRecord $record): ?ActiveRecord
    {
        if ($record->validate() && $record->save()) {
            return $record;
        }

        $this->addError(implode(' ', $record->getErrorSummary(true)));
        return null;
    }

    protected function attachFiles(BaseElementContent $record, array $files): void
    {
        $updateRecord = false;

        $newFiles = [];
        foreach ($files as $fileData) {
            $file = new FileContent();
            foreach ($fileData as $attribute => $value) {
                if ($attribute === 'base64Content') {
                    $file->newFileContent = base64_decode((string) $value);
                }
                if (!in_array($attribute, ['guid', 'object_model', 'object_id']) && $file->hasAttribute($attribute)) {
                    $file->$attribute = $value;
                }
            }

            if ($file = $this->saveRecord($file)) {
                $newGuid = $file->guid;
                $newFiles[] = $file;
            } else {
                $newGuid = '';
            }

            foreach ($record->dyn_attributes as $attribute => $value) {
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
            $this->saveRecord($record);
        }
    }

    /**
     * Check the importing file is compatible with current version
     *
     * @param string $version
     * @param array $data
     * @return bool
     */
    protected function checkVersion(string $version, array $data): bool
    {
        return isset($data['version']) && $version === $data['version'];
    }
}
