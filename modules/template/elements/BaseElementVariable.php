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

    public int $id;

    public bool $empty;
    protected bool $inEditMode = false;

    public function __construct(BaseElementContent $elementContent, string $mode = 'edit')
    {
        $this->elementContent = $elementContent;
        if ($mode === 'edit') {
            $this->inEditMode = true;
        }

        $this->id = $elementContent->id;
        $this->empty = $elementContent->isEmpty();

    }

    public function __toString()
    {
        return strval($this->elementContent);
    }

}
