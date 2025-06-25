<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\elements;

class BaseContentRecordsElementVariable extends BaseElementVariableIterator
{
    public function __construct(BaseContentRecordsElement $elementContent)
    {
        parent::__construct($elementContent);

        foreach ($elementContent->getItems() as $record) {
            $this->items[] = BaseContentRecordElementVariable::instance($elementContent)
                ->setRecord($record);
        }
    }
}
