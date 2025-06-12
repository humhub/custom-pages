<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\elements;

class UsersElementVariable extends BaseElementVariableIterator
{

    public function __construct(UsersElement $elementContent)
    {
        parent::__construct($elementContent);

        foreach ($elementContent->getItems() as $user) {
            $userVariable = new UserElementVariable($elementContent);
            $userVariable->setContentContainer($user);

            $this->items[] = $userVariable;
        }
    }
}
