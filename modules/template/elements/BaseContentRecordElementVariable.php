<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\elements;

use humhub\modules\content\components\ContentActiveRecord;
use yii\db\ActiveRecord;

class BaseContentRecordElementVariable extends BaseRecordElementVariable
{
    public ?UserElementVariable $author;

    public function setRecord(?ActiveRecord $record): self
    {
        parent::setRecord($record);

        if ($this->record instanceof ContentActiveRecord) {
            $this->author = UserElementVariable::instance($this->elementContent)
                ->setRecord($this->record->createdBy);
        }

        return $this;
    }
}
