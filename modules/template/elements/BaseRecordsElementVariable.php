<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\elements;

class BaseRecordsElementVariable extends BaseElementVariableIterator
{
    public function __construct(BaseRecordsElement $elementContent)
    {
        parent::__construct($elementContent);

        foreach ($elementContent->getItems() as $record) {
            $this->items[] = BaseRecordElementVariable::instance($elementContent)->setRecord($record);
        }
    }
}
