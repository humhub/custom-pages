<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\elements;

use humhub\libs\Html;
use humhub\modules\custom_pages\modules\template\widgets\CollapsableFormGroup;
use humhub\modules\file\models\File;
use humhub\modules\file\libs\FileHelper;
use humhub\modules\file\widgets\FilePreview;
use humhub\modules\file\widgets\UploadButton;
use humhub\modules\file\widgets\UploadProgress;
use humhub\modules\ui\form\widgets\ActiveForm;
use Yii;
use yii\helpers\Url;

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
class FileDownloadElement extends BaseElementContent
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
            [['file_guid'], 'safe'],
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
    public function __toString()
    {
        $options = [];

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

        return Html::tag('a', $content, $options['htmlOptions']);
    }

    /**
     * @inheritdoc
     */
    public function renderEditForm(ActiveForm $form): string
    {
        $id = 'fileDownloadElement-' . $this->id;

        $result = $form->field($this, 'title') .
            $form->field($this, 'showFileinfo')->checkbox() .
            $form->field($this, 'showIcon')->checkbox() .
            Html::beginTag('div', ['id' => $id, 'class' => 'file-upload-container clearfix']) .
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
                    'buttonOptions' => ['style' => 'float:left;'],
                ]) .
                FilePreview::widget([
                    'id' => $id . '-preview',
                    'popoverPosition' => 'top',
                    'items' => [$this->getFile()],
                    'options' => ['style' => 'display:block;margin-left:150px']]) .
                UploadProgress::widget([
                    'id' => $id . '-progress',
                    'options' => ['style' => 'display:block;margin-left:150px;width:500px'],
                ]) .
            Html::endTag('div');

        ob_start();
        CollapsableFormGroup::begin([
            'defaultState' => false,
            'label' => Yii::t('CustomPagesModule.base', 'Advanced'),
        ]);
        echo $form->field($this, 'style') .
            $form->field($this, 'cssClass') .
            $form->field($this, 'file_guid')->hiddenInput(['class' => 'file-guid'])->label(false);
        CollapsableFormGroup::end();

        return $result . ob_get_flush();
    }
}
