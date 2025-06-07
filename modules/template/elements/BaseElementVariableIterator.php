<?php

namespace humhub\modules\custom_pages\modules\template\elements;

class BaseElementVariableIterator extends BaseElementVariable implements \Iterator
{
    protected array $items;

    private int $position;

    public function __construct(BaseElementContent $elementContent, string $mode = 'edit')
    {
        $this->items = [];
        $this->position = 0;
    }

    public function __toString()
    {
        return count($this->items);
    }

    public function current()
    {
        return $this->items[$this->position];
    }

    public function next()
    {
        ++$this->position;
    }

    public function key()
    {
        return $this->position;
    }

    public function valid()
    {
        return isset($this->items[$this->position]);
    }

    public function rewind()
    {
        $this->position = 0;
    }
}