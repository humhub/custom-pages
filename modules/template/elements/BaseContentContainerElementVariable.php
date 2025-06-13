<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\elements;

use humhub\modules\content\components\ContentContainerActiveRecord;
use yii\db\ActiveRecord;

class BaseContentContainerElementVariable extends BaseRecordElementVariable
{
    public string $displayName;
    public string $url;
    public string $guid;

    public function setRecord(?ActiveRecord $record): BaseRecordElementVariable
    {
        if ($record instanceof ContentContainerActiveRecord) {
            $this->displayName = $record->displayName;
            $this->url = $record->getUrl();
            $this->guid = $record->guid;
        }

        return parent::setRecord($record);
    }
}
