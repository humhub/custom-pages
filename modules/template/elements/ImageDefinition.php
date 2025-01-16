<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\elements;

use Yii;

/**
 * Class for template Image element content definition
 *
 * Dynamic attributes:
 * @property string $style
 * @property string $width
 * @property string $height
 */
class ImageDefinition extends BaseTemplateElementContentDefinition
{
    /**
     * @inheritdoc
     */
    protected function getDynamicAttributes(): array
    {
        return [
            'style' => null,
            'width' => null,
            'height' => null,
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['style'], 'string'],
            [['height', 'width'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'style' => Yii::t('CustomPagesModule.template', 'Style'),
            'height' => Yii::t('CustomPagesModule.template', 'Height'),
            'width' => Yii::t('CustomPagesModule.template', 'Width'),
        ]);
    }
}
