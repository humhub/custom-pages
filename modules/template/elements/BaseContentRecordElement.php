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

    public function isEmpty(): bool
    {
        return parent::isEmpty() || !$this->record;
    }

    /**
     * @inheritdoc
     */
    public function renderEmpty($options = [])
    {
        return $this->renderEmptyDiv(Yii::t('CustomPagesModule.model', 'Empty'), $options);
    }

    /**
     * @inheritdoc
     */
    public function setAttributes($values, $safeOnly = true)
    {
        if (isset($values['id'])) {
            $values['id'] = is_array($values['id']) ? array_shift($values['id']) : null;
        }

        parent::setAttributes($values, $safeOnly);
    }

    protected function getRecord(): ?ContentActiveRecord
    {
        if (empty($this->id)) {
            return null;
        }

        return Yii::$app->runtimeCache->getOrSet(static::class . static::RECORD_CLASS . $this->id, function () {
            return static::RECORD_CLASS::findOne($this->id);
        });
    }
}
