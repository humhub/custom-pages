<?php

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
            $authorVariable = new UserElementVariable($this->elementContent, ($this->inEditMode) ? 'edit' : null);
            $authorVariable->setContentContainer($contentActiveRecord->getCreatedBy());
            $this->author = $authorVariable;
        }
    }
}