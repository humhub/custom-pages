<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\elements;

use humhub\libs\Html;
use humhub\modules\ui\form\widgets\ActiveForm;
use Yii;

/**
 * Class to manage content records of the Text elements
 *
 * Dynamic attributes:
 * @property bool $inline_text
 * @property string $content
 */
class TextElement extends BaseElementContent
{
    /**
     * @inheritdoc
     */
    public function getLabel(): string
    {
        return Yii::t('CustomPagesModule.template', 'Text');
    }

    /**
     * @inheritdoc
     */
    protected function getDynamicAttributes(): array
    {
        return [
            'content' => null,
            'inline_text' => 1,
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['content', 'trim'],
            ['inline_text', 'boolean'],
            ['content', 'string', 'length' => [1, 255]],
        ];
    }

    /**
     * @inheritdoc
     */
    public function __get($name)
    {
        $value = parent::__get($name);

        if ($name === 'inline_text' && !$this->isDefault()) {
            // Always get this dynamic attribute from default content
            // TODO: for normal work we should move the option to definition,
            //       because it is editable only from back-office
            $value = $this->element?->getDefaultContent(true)?->inline_text;
        }

        return $value;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'content' => Yii::t('CustomPagesModule.template', 'Content'),
            'inline_text' => Yii::t('CustomPagesModule.template', 'Is inline text'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();

        // We disallow editing this field in page editor
        $index = array_search('inline_text', $scenarios[self::SCENARIO_EDIT]);
        if ($index !== false) {
            unset($scenarios[self::SCENARIO_EDIT][$index]);
        }

        return $scenarios;
    }

    public function __toString()
    {
        return $this->inline_text ? $this->purify($this->content) : Html::encode($this->content);
    }

    /**
     * @inheritdoc
     */
    public function renderEditForm(ActiveForm $form): string
    {
        $result = $form->field($this, 'content')->textInput(['maxlength' => 255])->label(false);

        if ($this->isAdminEditMode()) {
            $result .= $form->field($this, 'inline_text')->checkbox() .
                Html::tag(
                    'div',
                    Yii::t('CustomPagesModule.base', 'Select this setting for visible text nodes only. Uncheck this setting in case this element is used for example as HTML attribute value.'),
                    ['class' => 'alert alert-info'],
                );
        }

        return $result;
    }
}
