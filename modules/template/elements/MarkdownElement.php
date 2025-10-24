<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\elements;

use humhub\helpers\Html;
use humhub\modules\content\widgets\richtext\RichText;
use humhub\modules\content\widgets\richtext\RichTextField;
use humhub\widgets\form\ActiveForm;
use Yii;

/**
 * Class to manage content records of the HumHub RichText elements
 *
 * Dynamic attributes:
 * @property string $content
 */
class MarkdownElement extends BaseElementContent implements \Stringable
{
    /**
     * @inheritdoc
     */
    public function getLabel(): string
    {
        return Yii::t('CustomPagesModule.template', 'Markdown');
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
    public function __toString(): string
    {
        return (string) Richtext::output($this->content);
    }

    /**
     * @inheritdoc
     */
    public function saveFiles()
    {
        Richtext::postProcess($this->content, $this);
    }

    /**
     * @inheritdoc
     */
    public function renderEditForm(ActiveForm $form): string
    {
        $result = $form->field($this, 'content')->widget(RichTextField::class);

        foreach ($this->fileList as $file) {
            $result .= Html::hiddenInput($this->formName() . '[fileList][]', $file);
        }

        return $result;
    }
}
