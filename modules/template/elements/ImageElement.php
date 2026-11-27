<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\elements;

use humhub\helpers\Html;
use humhub\modules\custom_pages\modules\template\widgets\CollapsableFormGroup;
use humhub\modules\custom_pages\modules\template\widgets\DeleteContentButton;
use humhub\modules\file\libs\FileHelper;
use humhub\modules\file\widgets\UploadButton;
use humhub\modules\file\widgets\UploadProgress;
use humhub\widgets\form\ActiveForm;
use humhub\widgets\JsWidget;
use Yii;

/**
 * Class to manage content records of the Image elements
 *
 * Dynamic attributes:
 * @property string $alt
 */
class ImageElement extends FileElement
{
    /**
     * @inheritdoc
     */
    public function getLabel(): string
    {
        return Yii::t('CustomPagesModule.template', 'Image');
    }

    /**
     * @inheritdoc
     */
    public $definitionModel = ImageDefinition::class;

    /**
     * @inheritdoc
     */
    protected function getDynamicAttributes(): array
    {
        return array_merge(parent::getDynamicAttributes(), [
            'alt' => null,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $result = [];
        // We prevent the content instance from being saved if there is no definition setting, to get sure we have an empty content in this case
        // TODO: perhaps overwrite the validate method and call parent validate only if no definition is set
        if ($this->definition == null || !$this->definition->hasValues()) {
            $result[] = [['file_guid'], 'required'];
        }
        $result[] = [['alt', 'file_guid'], 'safe'];
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'alt' =>  Yii::t('CustomPagesModule.base', 'Alternate text'),
        ]);
    }

    /**
     * @inheritdoc
     */
    /**
     * @inheritdoc
     */
    public static function isInlineEditingEnabledByDefault(): bool
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function getTemplateInstanceTitleValue(): ?string
    {
        return empty($this->alt) ? parent::getTemplateInstanceTitleValue() : $this->alt;
    }

    public function __toString(): string
    {
        if (!$this->hasFile()) {
            return '';
        }

        return (string) Html::tag('img', '', [
            'src' => $this->getFile()->getUrl(),
            'alt' => $this->purify($this->alt),
            'height' => $this->purify($this->definition->height),
            'width' => $this->purify($this->definition->width),
            'style' => $this->purify($this->definition->style),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getTemplateVariable(): BaseElementVariable
    {
        return ImageElementVariable::instance($this)
            ->setRecord($this->getFile());
    }

    /**
     * @inheritdoc
     */
    public function renderEditForm(ActiveForm $form): string
    {
        $id = 'imageElement-' . $this->id;
        $file = $this->getFile();

        return $form->field($this, 'file_guid')->hiddenInput(['class' => 'file-guid'])->label(false)

        . Html::beginTag('div', ['id' => $id])
            . Html::beginTag('div', ['class' => 'row'])
                . Html::beginTag('div', ['class' => 'col-md-4 uploadContainer'])
                    . UploadButton::widget([
                        'cssButtonClass' => 'btn-primary',
                        'model' => $this,
                        'single' => true,
                        'label' => true,
                        'attribute' => 'file_guid',
                        'dropZone' => '#' . $id,
                        'tooltip' => Yii::t('CustomPagesModule.base', 'Upload image'),
                        'preview' => '#' . $id . '-preview',
                        'progress' => '#' . $id . '-progress',
                    ]) . ' '
                    . DeleteContentButton::widget([
                        'model' => $this,
                        'previewId' => $id . '-preview',
                    ])
                . Html::endTag('div')

                . UploadProgress::widget(['id' => $id . '-progress', 'options' => ['style' => 'width:500px']])
                // The image, no file rows: the JS widget shows the file it starts with and an uploaded one
                . JsWidget::widget([
                    'id' => $id . '-preview',
                    'jsWidget' => 'custom_pages.template.ImagePreview',
                    'init' => $file ? [FileHelper::getFileInfos($file)] : [],
                    'visible' => $file !== null,
                    'options' => ['class' => 'col-md-8 previewContainer'],
                ])
            . Html::endTag('div')

            . Html::tag('br')

            . $this->renderCollapsableEditForm($form)

        . Html::endTag('div');
    }

    private function renderCollapsableEditForm(ActiveForm $form): string
    {
        $disableDefinition = !$this->isAdminEditMode();

        ob_start();
        CollapsableFormGroup::begin(['defaultState' => false]);

        echo Html::beginTag('div', ['class' => 'row'])
            . Html::beginTag('div', ['class' => 'col-md-6'])
                . $form->field($this->definition, 'height')->textInput(['disabled' => $disableDefinition])
            . Html::endTag('div')
            . Html::beginTag('div', ['class' => 'col-md-6'])
                . $form->field($this->definition, 'width')->textInput(['disabled' => $disableDefinition])
            . Html::endTag('div')
        . Html::endTag('div')

        . $form->field($this->definition, 'style')->textInput(['disabled' => $disableDefinition])

        . $form->field($this, 'alt');

        CollapsableFormGroup::end();

        return ob_get_clean();
    }
}
