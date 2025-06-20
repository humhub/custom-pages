<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\elements;

use humhub\components\ActiveRecord;
use humhub\libs\Html;
use humhub\modules\custom_pages\modules\template\interfaces\TemplateElementContentIterable;
use Yii;
use yii\db\ActiveQuery;

/**
 * Abstract class to manage content records of the elements with different object list (Spaces, Users)
 *
 * Dynamic attributes:
 * @property string $type
 * @property array $static
 */
abstract class BaseRecordsElement extends BaseElementContent implements TemplateElementContentIterable
{
    public const RECORD_CLASS = null;

    /**
     * @var ActiveRecord[]|null
     */
    protected ?array $records = null;

    /**
     * @var string A view file to render a widget with form fields for the Records
     */
    public string $subFormView = '';

    /**
     * Get query of the records depending on config
     *
     * @return ActiveQuery
     */
    abstract protected function getQuery(): ActiveQuery;

    /**
     * @inheritdoc
     */
    protected function getDynamicAttributes(): array
    {
        return [
            'type' => null,
            'static' => null,
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'type' => Yii::t('CustomPagesModule.template', 'Type'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type'], 'in', 'range' => array_keys($this->getTypes())],
            [['static'], 'safe'],
        ];
    }

    public function __toString()
    {
        return Html::encode(static::RECORD_CLASS);
    }

    /**
     * @inheritdoc
     */
    public function getFormView(): string
    {
        return 'elements/records';
    }

    /**
     * Get types for the records list
     *
     * @return array
     */
    public function getTypes(): array
    {
        return [
            'static' => Yii::t('CustomPagesModule.template', 'Static list'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getItems(): iterable
    {
        if ($this->records === null) {
            if (!$this->isConfigured()) {
                // No need to touch DB because option is not configured for the current type
                $this->records = [];
            } else {
                // Get records from DB
                $query = $this->getQuery();

                if ($this->type !== 'static' && !empty($this->limit)) {
                    // Limit only dynamic list
                    $query->limit($this->limit);
                }

                $this->records = $query->all();
            }
        }

        yield from $this->records;
    }

    /**
     * Filter the list with static selected record
     *
     * @param ActiveQuery $query
     * @return ActiveQuery
     */
    protected function filterStatic(ActiveQuery $query): ActiveQuery
    {
        return $query->andWhere(['guid' => $this->static]);
    }

    /**
     * Check if the Element is properly configured
     *
     * @return bool
     */
    protected function isConfigured(): bool
    {
        return !empty($this->{$this->type});
    }
}
