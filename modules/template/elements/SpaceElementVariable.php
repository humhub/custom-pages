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
        if ($this->contentContainer instanceof Space) {
            return $this->contentContainer;
        }
    }

    public function getMemberCount(): int
    {
        if ($this->getSpace() === null) {
            return -1;
        }

        return (new MemberListService($this->getSpace()))->getCount();
    }

}