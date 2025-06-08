<?php

namespace humhub\modules\custom_pages\modules\template\elements;

class UsersElementVariable extends BaseElementVariableIterator
{

    public function __construct(UsersElement $elementContent, string $mode = 'edit')
    {
        parent::__construct($elementContent, $mode);

        foreach ($elementContent->getItems() as $user) {
            $userVariable = new UserElementVariable($elementContent, $mode);
            $userVariable->setContentContainer($user);

            $this->items[] = $userVariable;
        }
    }
}