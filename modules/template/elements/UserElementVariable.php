<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\elements;

use humhub\modules\custom_pages\lib\templates\twig\TwigEngine;
use humhub\modules\friendship\models\Friendship;
use humhub\modules\user\models\User;

class UserElementVariable extends BaseContentContainerElementVariable
{
    public function __construct(BaseElementContent $elementContent)
    {
        parent::__construct($elementContent);
        TwigEngine::registerSandboxExtensionAllowedFunctions(static::class, ['getFriendCount']);
    }

    private function getUser(): ?User
    {
        return $this->contentContainer instanceof User ? $this->contentContainer : null;
    }

    public function getFriendCount(): int
    {
        $user = $this->getUser();

        if ($user === null) {
            return -1;
        }

        return Friendship::getFriendsQuery($user)->count();
    }

}
