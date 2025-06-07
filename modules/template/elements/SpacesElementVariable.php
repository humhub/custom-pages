<?php

namespace humhub\modules\custom_pages\modules\template\elements;

class SpacesElementVariable extends BaseElementVariableIterator
{

    public function __construct(SpacesElement $elementContent, string $mode = 'edit')
    {
        parent::__construct($elementContent, $mode);

        foreach ($elementContent->getItems() as $space) {
            $spaceVariable = new SpaceElementVariable($elementContent, $mode);
            $spaceVariable->setContentContainer($space);

            $this->items[] = $spaceVariable;
        }
    }
}