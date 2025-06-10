<?php

namespace humhub\modules\custom_pages\modules\template\elements;

use humhub\modules\custom_pages\lib\templates\twig\TwigEngine;
use humhub\modules\space\models\Space;
use humhub\modules\space\services\MemberListService;

class SpaceElementVariable extends BaseContentContainerElementVariable
{
    public function __construct(BaseElementContent $elementContent, string $mode = 'edit')
    {
        parent::__construct($elementContent, $mode);
        TwigEngine::registerSandboxExtensionAllowedFunctions(static::class, ['getMemberCount']);
    }

    private function getSpace(): ?Space
    {
        return $this->contentContainer instanceof Space ? $this->contentContainer : null;
    }

    public function getMemberCount(): int
    {
        if ($this->getSpace() === null) {
            return -1;
        }

        return (new MemberListService($this->getSpace()))->getCount();
    }

}
