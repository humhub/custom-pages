<?php

namespace humhub\modules\custom_pages\modules\template\elements;

use humhub\modules\custom_pages\lib\templates\twig\TwigEngine;
use humhub\modules\friendship\models\Friendship;
use humhub\modules\user\models\User;

class UserElementVariable extends BaseContentContainerElementVariable
{
    public function __construct(BaseElementContent $elementContent, string $mode = 'edit')
    {
        parent::__construct($elementContent, $mode);
        TwigEngine::registerSandboxExtensionAllowedFunctions(static::class, ['getFriendCount']);
    }

    private function getUser(): ?User
    {
        if ($this->contentContainer instanceof User) {
            return $this->contentContainer;
        }
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