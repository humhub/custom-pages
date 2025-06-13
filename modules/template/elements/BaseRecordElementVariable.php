<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\elements;

use yii\db\ActiveRecord;

class BaseRecordElementVariable extends BaseElementVariable
{
    protected ?ActiveRecord $record = null;

    public function setRecord(?ActiveRecord $record): self
    {
        $this->record = $record;
        return $this;
    }
}
