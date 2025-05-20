<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\models\forms;

use humhub\modules\custom_pages\modules\template\models\TemplateElement;
use humhub\modules\custom_pages\modules\template\models\TemplateInstance;
use humhub\modules\custom_pages\modules\template\services\ImportInstanceService;
use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class ImportInstanceForm extends Model
{
    /**
     * @var TemplateInstance
     */
    public $instance;

    /**
     * @var TemplateElement|null
     */
    public $element;

    /**
     * @var UploadedFile
     */
    public $file;

    public ?ImportInstanceService $service = null;

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
            'file' => Yii::t('CustomPagesModule.template', 'Upload file'),
        ];
    }

    public function attributeHints()
    {
        return [
            'file' => Yii::t('CustomPagesModule.template', 'File with template instance data in JSON format.'),
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

    public function getService(): ImportInstanceService
    {
        if ($this->service === null) {
            $this->service = new ImportInstanceService($this->instance, $this->element);
        }

        return $this->service;
    }

}
