<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\elements;

use humhub\modules\content\widgets\richtext\RichText;
use Yii;

/**
 * Class to manage content records of the HumHub RichText elements
 *
 * Dynamic attributes:
 * @property string $content
 */
class HumHubRichtextElement extends BaseElementContent
{
    /**
     * @inheritdoc
     */
    public function getLabel(): string
    {
        return Yii::t('CustomPagesModule.template', 'HumHub Richtext');
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
        return Richtext::output($this->content);
    }

    /**
     * @inheritdoc
     */
    public function saveFiles()
    {
        Richtext::postProcess($this->content, $this);
    }
}
