<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\elements;

use humhub\libs\Html;
use humhub\modules\custom_pages\modules\template\widgets\DeleteContentButton;
use humhub\modules\file\models\File;
use humhub\modules\file\widgets\FilePreview;
use humhub\modules\file\widgets\UploadButton;
use humhub\modules\file\widgets\UploadProgress;
use humhub\modules\ui\form\widgets\ActiveForm;
use Yii;

/**
 * Class to manage content records of the File elements
 *
 * Dynamic attributes:
 * @property string $file_guid
 */
class FileElement extends BaseElementContent
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
    public function __toString()
    {
        if ($this->hasFile()) {
            return $this->getFile()->getUrl();
        }
        return '';
    }

    /**
     * @inheritdoc
     */
    public function isEmpty(): bool
    {
        return !$this->hasFile();
    }

    /**
     * @inheritdoc
     */
    public function renderEditForm(ActiveForm $form): string
    {
        $id = 'fileElement-' . $this->id;

        return $form->field($this, 'file_guid')->hiddenInput(['class' => 'file-guid'])->label(false) .

        Html::beginTag('div', ['id' => $id, 'class' => 'file-upload-container clearfix']) .
            Html::beginTag('div', ['class' => 'row']) .

                Html::beginTag('div', ['class' => 'col-md-4 uploadContainer']) .
                    UploadButton::widget([
                        'cssButtonClass' => 'btn-primary',
                        'model' => $this,
                        'single' => true,
                        'label' => true,
                        'attribute' => 'file_guid',
                        'dropZone' => '#' . $id,
                        'tooltip' => false,
                        'preview' => '#' . $id . '-preview',
                        'progress' => '#' . $id . '-progress',
                    ]) . ' ' .
                    DeleteContentButton::widget([
                        'model' => $this,
                        'previewId' => $id . '-preview',
                    ]) .
                Html::endTag('div') .

                Html::beginTag('div', ['class' => 'col-md-8 previewContainer']) .
                    FilePreview::widget([
                        'id' => $id . '-preview',
                        'popoverPosition' => 'top',
                        'items' => [$this->getFile()],
                    ]) .
                    UploadProgress::widget(['id' => $id . '-progress']) .
                Html::endTag('div') .

            Html::endTag('div') .
        Html::endTag('div');
    }
}
