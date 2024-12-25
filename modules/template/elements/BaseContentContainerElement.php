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
use humhub\modules\custom_pages\modules\template\widgets\TemplateContentFormFields;
use yii\db\IntegrityException;

/**
 * Abstract class to manage content records of the User/Space elements
 *
 * Dynamic attributes:
 * @property string $guid
 */
abstract class BaseContentContainerElement extends BaseTemplateElementContent
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
    public function getLabel()
    {
        return static::$label;
    }

    /**
     * @inheritdoc
     */
    public function render($options = [])
    {
        return Html::encode($this->getRecord()->getDisplayName());
    }

    /**
     * @inheritdoc
     */
    public function renderEmpty($options = [])
    {
        return '';
    }

    /**
     * @inheritdoc
     */
    public function renderForm($form)
    {
        return TemplateContentFormFields::widget([
            'type' => strtolower(substr(strrchr(static::CONTAINER_CLASS, '\\'), 1)),
            'form' => $form,
            'model' => $this,
        ]);
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
     * @inheritdoc
     */
    public function beforeValidate()
    {
        return parent::beforeValidate();
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
}
