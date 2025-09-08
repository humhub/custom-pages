<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\elements;

abstract class BaseContentContainersElementVariable extends BaseElementVariableIterator
{
    /**
     * Class name for single item(Space/User) variable
     *
     * @var string|BaseContentContainerElementVariable|SpaceElementVariable|UserElementVariable
     */
    public const ITEM_VARIABLE_CLASS = null;

    public function __construct(BaseContentContainersElement $elementContent)
    {
        parent::__construct($elementContent);

        foreach ($elementContent->getItems() as $contentContainerActiveRecord) {
            $this->items[] = static::ITEM_VARIABLE_CLASS::instance($elementContent)
                ->setRecord($contentContainerActiveRecord);
        }
    }
}
