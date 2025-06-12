<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\elements;

use humhub\modules\content\components\ContentActiveRecord;

class BaseContentRecordElementVariable extends BaseElementVariable
{
    protected ?ContentActiveRecord $contentActiveRecord = null;

    public ?UserElementVariable $author;

    public function setContent(?ContentActiveRecord $contentActiveRecord): void
    {
        $this->contentActiveRecord = $contentActiveRecord;

        if ($this->contentActiveRecord !== null) {
            $authorVariable = new UserElementVariable($this->elementContent);
            $authorVariable->setContentContainer($contentActiveRecord->createdBy);
            $this->author = $authorVariable;
        }
    }

    public function __isset($name): bool
    {
        return property_exists($this, $name) || isset($this->contentActiveRecord->$name);
    }

    public function __get($name)
    {
        return $this->$name ?? $this->contentActiveRecord->$name ?? null;
    }
}
