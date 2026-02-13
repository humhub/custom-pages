<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\elements;

use humhub\helpers\Html;
use humhub\modules\activity\interfaces\ConfigurableActivityInterface;
use humhub\modules\activity\models\Activity;
use humhub\modules\activity\services\ActivityManager;
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
        $baseActivity = ActivityManager::load($this->record);

        return (string) Html::encode($baseActivity instanceof ConfigurableActivityInterface
            ? $baseActivity->getTitle()
            : $this->contentId);
    }

    /**
     * @inheritdoc
     */
    public function getTemplateVariable(): BaseElementVariable
    {
        return ActivityElementVariable::instance($this)->setRecord($this->getRecord());
    }
}
