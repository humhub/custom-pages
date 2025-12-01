<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\elements;

use humhub\modules\activity\models\Activity;
use Yii;

/**
 * Class to manage content records of the elements with Activities list
 */
class ActivitiesElement extends BaseContentRecordsElement
{
    public const RECORD_CLASS = Activity::class;

    /**
     * @inheritdoc
     */
    public function getLabel(): string
    {
        return Yii::t('CustomPagesModule.base', 'Activities');
    }

    /**
     * @inheritdoc
     */
    public function getTemplateVariable(): BaseElementVariable
    {
        return new ActivitiesElementVariable($this);
    }
}
