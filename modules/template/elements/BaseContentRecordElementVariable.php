<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\elements;

use humhub\modules\content\components\ContentActiveRecord;
use humhub\modules\custom_pages\lib\templates\twig\TwigEngine;

class BaseContentRecordElementVariable extends BaseRecordElementVariable
{
    public ?UserElementVariable $_author = null;

    public function __construct(BaseElementContent $elementContent)
    {
        parent::__construct($elementContent);
        TwigEngine::registerSandboxExtensionAllowedFunctions(static::class, ['getAuthor']);
    }

    public function getAuthor(): ?UserElementVariable
    {
        if ($this->_author === null && $this->record instanceof ContentActiveRecord) {
            $this->_author = UserElementVariable::instance($this->elementContent)
                ->setRecord($this->record->createdBy);
        }

        return $this->_author;
    }
}
