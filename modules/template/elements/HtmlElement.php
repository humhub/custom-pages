<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\elements;

use humhub\helpers\Html;
use humhub\modules\custom_pages\widgets\TinyMce;
use humhub\modules\file\widgets\FilePreview;
use humhub\modules\file\widgets\UploadButton;
use humhub\modules\file\widgets\UploadProgress;
use humhub\widgets\form\ActiveForm;
use Yii;

/**
 * Class to manage content records of the Html elements
 *
 * Dynamic attributes:
 * @property string $content
 */
class HtmlElement extends BaseElementContent
{
    /**
     * @inheritdoc
     */
    public function getLabel(): string
    {
        return Yii::t('CustomPagesModule.template', 'Html');
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
                'id' => 'html_' . $id,
                'class' => 'tinymceInput',
                'rows' => 6,
            ],
            'clientOptions' => [
                'humhubTrigger' => [
                    'icon' => 'upload',
                    'text' => Yii::t('CustomPagesModule.model', 'Attach Files'),
                    'selector' => '#html-template-file-uploader-' . $id,
                    'event' => 'click',
                ],
            ]])->label(false)

            . Html::beginTag('div', ['class' => 'mb-3'])
                . UploadButton::widget([
                    'id' => 'html-template-file-uploader-' . $id,
                    'label' => Yii::t('CustomPagesModule.model', 'Attach Files'),
                    'tooltip' => false,
                    'progress' => '#html-template-file-uploader-progress-' . $id,
                    'preview' => '#html-template-file-uploader-preview-' . $id,
                    'cssButtonClass' => 'btn-light btn-sm',
                    'model' => $this,
                    'submitName' => $this->formName() . '[fileList][]',
                ])
                . FilePreview::widget([
                    'id' => 'html-template-file-uploader-preview-' . $id,
                    'options' => ['style' => 'margin-top:10px'],
                    'model' => $this,
                    'edit' => true,
                ])
                . UploadProgress::widget(['id' => 'html-template-file-uploader-progress-' . $id])
            . Html::endTag('div');
    }
}
