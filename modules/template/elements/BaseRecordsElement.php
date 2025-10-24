<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\elements;

use humhub\components\ActiveRecord;
use humhub\helpers\Html;
use humhub\modules\custom_pages\modules\template\interfaces\TemplateElementContentIterable;
use yii\db\ActiveQuery;

/**
 * Abstract class to manage Active Records of the elements
 */
abstract class BaseRecordsElement extends BaseElementContent implements TemplateElementContentIterable, \Stringable
{
    public const RECORD_CLASS = null;

    /**
     * @var ActiveRecord[]|null
     */
    protected ?array $records = null;

    /**
     * Get query of the records depending on filters
     *
     * @return ActiveQuery
     */
    abstract protected function getQuery(): ActiveQuery;

    public function __toString(): string
    {
        return (string) Html::encode(static::RECORD_CLASS);
    }

    /**
     * @inheritdoc
     */
    public function getItems(): iterable
    {
        if ($this->records === null) {
            $this->records = $this->getQuery()->all();
        }

        yield from $this->records;
    }

    /**
     * @inheritdoc
     */
    public function isCacheable(): bool
    {
        // Allow cache only when you are sure the Active Records are not filtered for current User
        return false;
    }
}
