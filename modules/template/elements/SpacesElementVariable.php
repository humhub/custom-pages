<?php

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
