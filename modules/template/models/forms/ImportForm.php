<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\models\forms;

use humhub\modules\custom_pages\modules\template\services\TemplateImportService;
use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class ImportForm extends Model
{
    public $file;

    public ?TemplateImportService $service = null;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['file'], 'file', 'extensions' => 'json', 'checkExtensionByMimeType' => false, 'skipOnEmpty' => false],
        ];
    }

    public function attributeLabels()
    {
        return [
            'file' => Yii::t('CustomPagesModule.template', 'Upload Template'),
        ];
    }

    public function attributeHints()
    {
        return [
            'file' => Yii::t('CustomPagesModule.template', 'Upload a JSON file containing your template source data.'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeValidate()
    {
        $this->file = UploadedFile::getInstance($this, 'file');

        return parent::beforeValidate();
    }

    public function import(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        if (!$this->getService()->importFromFile($this->file->tempName)) {
            $this->addError('file', implode(' ', $this->getService()->getErrors()));
            return false;
        }

        return true;
    }

    public function getService(): TemplateImportService
    {
        if ($this->service === null) {
            $this->service = new TemplateImportService();
        }

        return $this->service;
    }

}
