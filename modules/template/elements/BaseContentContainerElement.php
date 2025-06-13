<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\elements;

use humhub\libs\Html;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\content\models\ContentContainer;
use yii\db\IntegrityException;

/**
 * Abstract class to manage content records of the User/Space elements
 *
 * Dynamic attributes:
 * @property string $guid
 */
abstract class BaseContentContainerElement extends BaseElementContent
{
    public const CONTAINER_CLASS = null;

    protected ?ContentContainerActiveRecord $record = null;

    /**
     * @inheritdoc
     */
    protected function getDynamicAttributes(): array
    {
        return [
            'guid' => null,
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['guid'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function __toString()
    {
        return Html::encode($this->getRecord()?->getDisplayName());
    }

    /**
     * @inheritdoc
     */
    public function getFormView(): string
    {
        return 'elements/' . strtolower(substr(strrchr(static::CONTAINER_CLASS, '\\'), 1));
    }

    /**
     * @inheritdoc
     */
    public function setAttributes($values, $safeOnly = true)
    {
        if (isset($values['guid'])) {
            $values['guid'] = is_array($values['guid']) ? array_shift($values['guid']) : null;
        }

        parent::setAttributes($values, $safeOnly);
    }

    /**
     * Get a related record(User or Space) to the container
     *
     * @return ContentContainerActiveRecord|null
     * @throws IntegrityException
     */
    public function getRecord(?string $guid = null): ?ContentContainerActiveRecord
    {
        if ($this->record === null) {
            $this->record = ContentContainer::findRecord($guid ?: $this->guid);
        }

        return $this->record;
    }

    /**
     * @inheritdoc
     */
    public function getTemplateVariable(): BaseElementVariable
    {
        return BaseRecordElementVariable::instance($this)
            ->setRecord($this->getRecord());
    }
}
