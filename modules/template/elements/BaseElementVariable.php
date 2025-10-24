<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\elements;

class BaseElementVariable implements \Stringable
{
    public int $elementContentId;

    public bool $empty;

    public function __construct(protected BaseElementContent $elementContent)
    {
        $this->elementContentId = $this->elementContent->id ?? 0;
        $this->empty = $this->elementContent->isEmpty();
    }

    public static function instance(BaseElementContent $elementContent): static
    {
        return new static($elementContent);
    }

    public function __toString(): string
    {
        return (string) strval($this->elementContent);
    }

}
