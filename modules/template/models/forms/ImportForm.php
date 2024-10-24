<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\models\forms;

use humhub\modules\custom_pages\modules\template\models\Template;
use humhub\modules\custom_pages\modules\template\services\ImportService;
use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class ImportForm extends Model
{
    public $type;
    public $file;

    public ?ImportService $service = null;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type'], 'in', 'range' => [Template::TYPE_LAYOUT, Template::TYPE_SNIPPED_LAYOUT, Template::TYPE_CONTAINER]],
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
            'file' => Yii::t('CustomPagesModule.template', 'File with template source data in JSON format.'),
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

        if (!$this->getService()->run()) {
            $this->addError('file', implode(' ', $this->getService()->getErrors()));
            return false;
        }

        return true;
    }

    public function getService(): ImportService
    {
        if ($this->service === null) {
            $this->service = new ImportService($this->type, $this->file->tempName);
        }

        return $this->service;
    }

}
