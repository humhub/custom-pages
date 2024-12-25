<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\elements;

use humhub\modules\custom_pages\modules\template\widgets\TemplateContentFormFields;
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
    public static $label = 'File';

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
     * @inerhitdoc
     */
    public function attributeLabels()
    {
        return  [
            'file_guid' => Yii::t('CustomPagesModule.base', 'File'),
        ];
    }

    /**
     * @inerhitdoc
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
     * @inerhitdoc
     */
    public function getLabel()
    {
        return static::$label;
    }

    /**
     * @inerhitdoc
     */
    public function getFile()
    {
        return File::findOne(['guid' => $this->file_guid]);
    }

    /**
     * @inerhitdoc
     */
    public function hasFile()
    {
        return $this->file_guid != null && $this->getFile() != null;
    }

    /**
     * @inerhitdoc
     */
    public function getUrl()
    {
        $file = $this->getFile();
        return ($file != null) ? $file->getUrl() : null;
    }

    /**
     * @inerhitdoc
     */
    public function render($options = [])
    {
        if ($this->hasFile()) {
            return $this->getFile()->getUrl();
        }
        return '';
    }

    /**
     * @inerhitdoc
     */
    public function renderEmpty($options = [])
    {
        return '';
    }

    /**
     * @inerhitdoc
     */
    public function renderForm($form)
    {
        return TemplateContentFormFields::widget([
            'type' => 'file',
            'form' => $form,
            'model' => $this,
        ]);
    }

    /**
     * @inerhitdoc
     */
    public function isEmpty(): bool
    {
        return !$this->hasFile();
    }

}
