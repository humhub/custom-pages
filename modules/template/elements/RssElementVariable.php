<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\elements;

class RssElementVariable extends BaseElementVariableIterator
{
    public function __construct(RssElement $elementContent)
    {
        parent::__construct($elementContent);

        foreach ($elementContent->getItems() as $item) {
            $this->items[] = $item;
        }
    }
}
