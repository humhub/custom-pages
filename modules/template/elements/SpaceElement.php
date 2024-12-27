<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\elements;

use humhub\modules\space\models\Space;
use Yii;

/**
 * Class to manage content records of the Space elements
 */
class SpaceElement extends BaseContentContainerElement
{
    public const CONTAINER_CLASS = Space::class;

    /**
     * @inheritdoc
     */
    public function getLabel(): string
    {
        return Yii::t('CustomPagesModule.template', 'Space');
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'guid' => Yii::t('CustomPagesModule.template', 'Select space'),
        ];
    }
}
