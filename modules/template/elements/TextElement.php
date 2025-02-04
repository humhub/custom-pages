<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\elements;

use humhub\libs\Html;
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

    /**
     * @inheritdoc
     */
    public function render($options = [])
    {
        $result = $this->inline_text ? $this->purify($this->content) : Html::encode($this->content);

        if ($this->isEditMode($options) && $this->inline_text) {
            return $this->wrap('span', $result, $options);
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function renderEmpty($options = [])
    {
        if ($this->inline_text) {
            $options['class'] = 'emptyBlock text';
            return $this->renderEmptyDiv(Yii::t('CustomPagesModule.model', 'Empty Text'), $options);
        }

        return '';
    }
}
