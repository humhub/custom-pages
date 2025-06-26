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
     * Get query of the records depending on config
     *
     * @return ActiveQuery
     */
    abstract protected function getQuery(): ActiveQuery;

    abstract protected function isConfigured(): bool;

    public function __toString()
    {
        return Html::encode(static::RECORD_CLASS);
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

                $this->records = $query->all();
            }
        }

        yield from $this->records;
    }

}
