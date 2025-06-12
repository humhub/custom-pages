<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\elements;

class SpacesElementVariable extends BaseElementVariableIterator
{

    public function __construct(SpacesElement $elementContent)
    {
        parent::__construct($elementContent);

        foreach ($elementContent->getItems() as $space) {
            $spaceVariable = new SpaceElementVariable($elementContent);
            $spaceVariable->setContentContainer($space);

            $this->items[] = $spaceVariable;
        }
    }
}
