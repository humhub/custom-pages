<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\elements;

use humhub\helpers\Html;
use humhub\modules\activity\models\Activity;
use Yii;

/**
 * Class to manage content record of the Activity
 *
 * @property-read Activity|null $record
 */
class ActivityElement extends BaseContentRecordElement implements \Stringable
{
    protected const RECORD_CLASS = Activity::class;

    /**
     * @inheritdoc
     */
    public function getLabel(): string
    {
        return Yii::t('CustomPagesModule.base', 'Activity');
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'contentId' => Yii::t('CustomPagesModule.base', 'Activity content ID'),
        ];
    }

    public function __toString(): string
    {
        return (string) Html::encode($this->record?->getActivityBaseClass()?->getTitle() ?: $this->contentId);
    }

    /**
     * @inheritdoc
     */
    public function getTemplateVariable(): BaseElementVariable
    {
        return ActivityElementVariable::instance($this)->setRecord($this->getRecord());
    }
}
