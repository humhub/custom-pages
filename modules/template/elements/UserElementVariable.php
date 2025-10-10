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
        TwigEngine::registerSandboxExtensionAllowedFunctions(static::class, ['getFriendCount', 'getProfile']);
    }

    private function getUser(): ?User
    {
        return $this->record instanceof User ? $this->record : null;
    }

    public function getFriendCount(): int
    {
        $user = $this->getUser();

        if ($user === null) {
            return -1;
        }

        return Friendship::getFriendsQuery($user)->count();
    }

    /**
     * Get a profile field value
     *
     * @param string $field Field name
     * @return string
     */
    public function getProfile(string $field): string
    {
        return $this->getUser()?->profile?->$field ?? '';
    }

}
