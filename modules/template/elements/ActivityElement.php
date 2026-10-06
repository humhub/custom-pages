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
use humhub\widgets\form\ActiveForm;
use Yii;

/**
 * Class to manage Activity record
 *
 * @property-read Activity|null $record
 *
 * Dynamic attributes:
 * @property string $id
 */
class ActivityElement extends BaseElementContent implements \Stringable
{
    /**
     * @inheritdoc
     */
    protected function getDynamicAttributes(): array
    {
        return [
            'id' => null,
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
        ];
    }

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
            'id' => Yii::t('CustomPagesModule.base', 'Activity ID'),
        ];
    }

    public function __toString(): string
    {
        $baseActivity = ActivityManager::load($this->record);

        return Html::encode($baseActivity instanceof ConfigurableActivityInterface
            ? $baseActivity->getTitle()
            : $this->id);
    }

    protected function getRecord(): ?Activity
    {
        if (empty($this->id)) {
            return null;
        }

        return Yii::$app->runtimeCache->getOrSet(self::class . $this->id, fn() => Activity::findOne($this->id));
    }

    /**
     * @inheritdoc
     */
    public function getTemplateVariable(): BaseElementVariable
    {
        return ActivityElementVariable::instance($this)->setRecord($this->getRecord());
    }

    /**
     * @inheritdoc
     */
    public function renderEditForm(ActiveForm $form): string
    {
        return $form->field($this, 'id');
    }
}
