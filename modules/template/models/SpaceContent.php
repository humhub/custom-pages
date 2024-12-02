<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\models;

use humhub\modules\space\models\Space;
use Yii;

/**
 * Class SpaceContent
 */
class SpaceContent extends ContentContainerContent
{
    public const CONTAINER_CLASS = Space::class;
    public static $label = 'Space';

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
