<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\elements;

use humhub\modules\file\models\File;
use Yii;

/**
 * Class to manage content records of the File elements
 *
 * Dynamic attributes:
 * @property string $file_guid
 */
class FileElement extends BaseTemplateElementContent
{
    /**
     * @inheritdoc
     */
    public function getLabel(): string
    {
        return Yii::t('CustomPagesModule.template', 'File');
    }

    /**
     * @inheritdoc
     */
    protected function getDynamicAttributes(): array
    {
        return [
            'file_guid' => null,
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['file_guid'], 'required'],
            [['file_guid'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'file_guid' => Yii::t('CustomPagesModule.base', 'File'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function saveFiles()
    {
        $files = File::findByRecord($this);

        foreach ($files as $file) {
            if ($file->guid !== $this->file_guid) {
                $file->delete();
            }
        }

        $this->fileManager->attach($this->file_guid);
    }

    /**
     * Get File
     *
     * @return File|null
     */
    public function getFile(): ?File
    {
        return empty($this->file_guid) ? null : File::findOne(['guid' => $this->file_guid]);
    }

    /**
     * Check if a File is found for this Element
     *
     * @return bool
     */
    public function hasFile(): bool
    {
        return $this->getFile() !== null;
    }

    /**
     * @return string|null
     */
    public function getUrl(): ?string
    {
        return $this->getFile()?->getUrl();
    }

    /**
     * @inheritdoc
     */
    public function render($options = [])
    {
        if ($this->hasFile()) {
            return $this->getFile()->getUrl();
        }
        return '';
    }

    /**
     * @inheritdoc
     */
    public function renderEmpty($options = [])
    {
        return '';
    }

    /**
     * @inheritdoc
     */
    public function isEmpty(): bool
    {
        return !$this->hasFile();
    }
}
