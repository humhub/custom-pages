<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\elements;

class BaseElementVariable
{
    protected BaseElementContent $elementContent;

    public int $elementContentId;

    public bool $empty;

    public function __construct(BaseElementContent $elementContent)
    {
        $this->elementContent = $elementContent;
        $this->elementContentId = $elementContent->id ?? 0;
        $this->empty = $elementContent->isEmpty();
    }

    public function __toString()
    {
        return strval($this->elementContent);
    }

}
