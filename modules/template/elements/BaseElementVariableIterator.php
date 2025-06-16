<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\elements;

class BaseElementVariableIterator extends BaseElementVariable implements \Iterator
{
    protected array $items;

    private int $position;

    public function __construct(BaseElementContent $elementContent)
    {
        parent::__construct($elementContent);

        $this->items = [];
        $this->position = 0;
    }

    public function __toString()
    {
        return count($this->items);
    }

    public function current(): mixed
    {
        return $this->items[$this->position];
    }

    public function next(): void
    {
        ++$this->position;
    }

    public function key(): mixed
    {
        return $this->position;
    }

    public function valid(): bool
    {
        return isset($this->items[$this->position]);
    }

    public function rewind(): void
    {
        $this->position = 0;
    }
}
