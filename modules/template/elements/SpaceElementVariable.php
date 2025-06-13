<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\elements;

use humhub\modules\custom_pages\lib\templates\twig\TwigEngine;
use humhub\modules\space\models\Space;
use humhub\modules\space\services\MemberListService;

class SpaceElementVariable extends BaseContentContainerElementVariable
{
    public function __construct(BaseElementContent $elementContent)
    {
        parent::__construct($elementContent);
        TwigEngine::registerSandboxExtensionAllowedFunctions(static::class, ['getMemberCount']);
    }

    private function getSpace(): ?Space
    {
        return $this->record instanceof Space ? $this->record : null;
    }

    public function getMemberCount(): int
    {
        if ($this->getSpace() === null) {
            return -1;
        }

        return (new MemberListService($this->getSpace()))->getCount();
    }

}
