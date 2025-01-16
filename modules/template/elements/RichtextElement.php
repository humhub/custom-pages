<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\elements;

use Yii;

/**
 * Class to manage content records of the RichText elements
 *
 * Dynamic attributes:
 * @property string $content
 */
class RichtextElement extends BaseTemplateElementContent
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
    public function render($options = [])
    {
        if ($this->isEditMode($options)) {
            return $this->wrap('div', $this->purify($this->content), $options);
        }

        return $this->purify($this->content);
    }

    /**
     * @inheritdoc
     */
    public function renderEmpty($options = [])
    {
        return $this->renderEmptyDiv(Yii::t('CustomPagesModule.model', 'Empty Richtext'), $options);
    }
}
