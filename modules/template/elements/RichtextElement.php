<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\elements;

use humhub\libs\Html;
use humhub\modules\custom_pages\widgets\TinyMce;
use humhub\modules\file\widgets\FilePreview;
use humhub\modules\file\widgets\UploadButton;
use humhub\modules\file\widgets\UploadProgress;
use humhub\modules\ui\form\widgets\ActiveForm;
use Yii;

/**
 * Class to manage content records of the RichText elements
 *
 * Dynamic attributes:
 * @property string $content
 */
class RichtextElement extends BaseElementContent
{
    /**
     * @inheritdoc
     */
    public function getLabel(): string
    {
        return Yii::t('CustomPagesModule.template', 'Richtext');
    }

    /**
     * @inheritdoc
     */
    protected function getDynamicAttributes(): array
    {
        return [
            'content' => null,
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['content', 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'content' => Yii::t('CustomPagesModule.template', 'Content'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function __toString()
    {
        return $this->purify($this->content);
    }

    /**
     * @inheritdoc
     */
    public function renderEditForm(ActiveForm $form): string
    {
        $id = $this->id ?? str_replace(['[', ']'], '', $this->formName());

        return $form->field($this, 'content')->widget(TinyMce::class, [
            'options' => [
                'id' => 'richtext_' . $id,
                'class' => 'tinymceInput',
                'rows' => 6,
            ],
            'clientOptions' => [
                'humhubTrigger' => [
                    'icon' => 'upload',
                    'text' => Yii::t('CustomPagesModule.model', 'Attach Files'),
                    'selector' => '#richtext-template-file-uploader-' . $id,
                    'event' => 'click',
                ],
            ]])->label(false) .

            Html::beginTag('div', ['class' => 'form-group']) .
                UploadButton::widget([
                    'id' => 'richtext-template-file-uploader-' . $id,
                    'label' => Yii::t('CustomPagesModule.model', 'Attach Files'),
                    'tooltip' => false,
                    'progress' => '#richtext-template-file-uploader-progress-' . $id,
                    'preview' => '#richtext-template-file-uploader-preview-' . $id,
                    'cssButtonClass' => 'btn-default btn-sm',
                    'model' => $this,
                    'submitName' => $this->formName().'[fileList][]',
                ]) .
                FilePreview::widget([
                    'id' => 'richtext-template-file-uploader-preview-' . $id,
                    'options' => ['style' => 'margin-top:10px'],
                    'model' => $this,
                    'edit' => true,
                ]) .
                UploadProgress::widget(['id' => 'richtext-template-file-uploader-progress-' . $id]) .
            Html::endTag('div');
    }
}
