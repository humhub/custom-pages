<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\elements;

class ActivitiesElementVariable extends BaseElementVariableIterator
{
    public function __construct(ActivitiesElement $elementContent)
    {
        parent::__construct($elementContent);

        foreach ($elementContent->getItems() as $activity) {
            $this->items[] = ActivityElementVariable::instance($elementContent)->setRecord($activity);
        }
    }
}
