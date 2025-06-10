<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\elements;

use humhub\modules\content\components\ContentActiveRecord;
use Yii;

/**
 * Abstract class to manage Element Content of the ContentActiveRecord
 *
 * @property-read ContentActiveRecord|null $record
 *
 * Dynamic attributes:
 * @property string $id
 */
abstract class BaseContentRecordElement extends BaseElementContent
{
    protected const RECORD_CLASS = ContentActiveRecord::class;

    /**
     * @inheritdoc
     */
    protected function getDynamicAttributes(): array
    {
        return [
            'contentRecordId' => null,
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['contentRecordId'], 'integer'],
        ];
    }

    public function isEmpty(): bool
    {
        return parent::isEmpty() || !$this->record;
    }

    /**
     * @inheritdoc
     */
    public function setAttributes($values, $safeOnly = true)
    {
        if (isset($values['contentRecordId'])) {
            $values['contentRecordId'] = is_array($values['contentRecordId']) ? array_shift($values['contentRecordId']) : null;
        }

        parent::setAttributes($values, $safeOnly);
    }

    protected function getRecord(): ?ContentActiveRecord
    {
        if (empty($this->contentRecordId)) {
            return null;
        }

        return Yii::$app->runtimeCache->getOrSet(static::class . static::RECORD_CLASS . $this->contentRecordId, function () {
            return static::RECORD_CLASS::findOne($this->contentRecordId);
        });
    }

    public function getTemplateVariable(): BaseElementVariable
    {
        $variable = new BaseContentRecordElementVariable($this);
        $variable->setContent($this->getRecord());
        return $variable;
    }
}
