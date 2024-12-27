<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\elements;

use humhub\modules\file\models\File;
use humhub\modules\file\libs\FileHelper;
use Yii;
use yii\helpers\Url;
use yii\helpers\Html;

/**
 * Class to manage content records of the File Download elements
 *
 * Dynamic attributes:
 * @property string $file_guid
 * @property string $title
 * @property string $style
 * @property string $cssClass
 * @property bool $showFileinfo
 * @property bool $showIcon
 */
class FileDownloadElement extends BaseTemplateElementContent
{
    /**
     * @inheritdoc
     */
    public function getLabel(): string
    {
        return Yii::t('CustomPagesModule.template', 'File Download');
    }

    /**
     * @inheritdoc
     */
    protected function getDynamicAttributes(): array
    {
        return [
            'file_guid' => null,
            'title' => null,
            'style' => null,
            'cssClass' => null,
            'showFileinfo' => 1,
            'showIcon' => 1,
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['file_guid'], 'required'],
            [['title', 'style', 'cssClass'], 'string'],
            [['showFileinfo', 'showIcon'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'file_guid' => Yii::t('CustomPagesModule.base', 'File'),
            'title' => Yii::t('CustomPagesModule.base', 'Title'),
            'style' => Yii::t('CustomPagesModule.base', 'Style'),
            'cssClass' => Yii::t('CustomPagesModule.base', 'Css Class'),
            'showFileinfo' => Yii::t('CustomPagesModule.base', 'Show additional file information (size)'),
            'showIcon' => Yii::t('CustomPagesModule.base', 'Add a file icon before the title'),
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

    public function getFile(): ?File
    {
        return File::findOne(['guid' => $this->file_guid]);
    }

    public function hasFile(): bool
    {
        return $this->file_guid != null && $this->getFile() != null;
    }

    public function getUrl(): ?string
    {
        $file = $this->getFile();
        return ($file != null) ? $file->getUrl() : null;
    }

    public function getDownloadUrl(): ?string
    {
        $file = $this->getFile();
        return $file ? Url::to(['/file/file/download', 'guid' => $file->guid]) : null;
    }

    /**
     * @inheritdoc
     */
    public function render($options = [])
    {
        if (!$this->hasFile()) {
            return '';
        }

        $file =  $this->getFile();
        $options['htmlOptions'] = [
            'href' => $this->getDownloadUrl(),
            'style' => Html::encode($this->style),
            'class' => Html::encode($this->cssClass),
            'target' => '_blank',
            'data-pjax-prevent' => '1',
        ];

        $content = ($this->title) ? $this->title : $file->file_name;
        $content = Html::encode($content);

        $fileInfo = FileHelper::getFileInfos($file);

        if ($this->showIcon) {
            $options['htmlOptions']['class'] .= ' mime ' . $fileInfo['mimeIcon'];
        }

        if ($this->showFileinfo) {
            $content .= Html::tag('small', ' - ' . $fileInfo['size_format'], ['class' => 'file-fileInfo']);
        }

        return $this->isEditMode($options)
            ? $this->wrap('a', $content, $options)
            : Html::tag('a', $content, $options['htmlOptions']);
    }

    /**
     * @inheritdoc
     */
    public function renderEmpty($options = [])
    {
        return '';
    }
}
