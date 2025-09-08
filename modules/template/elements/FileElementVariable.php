<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\elements;

use humhub\modules\file\models\File;
use yii\db\ActiveRecord;

class FileElementVariable extends BaseRecordElementVariable
{
    public string $guid;
    public ?string $name;
    public ?string $title;
    public ?string $mimeType;
    public int $size;

    public function setRecord(?ActiveRecord $record): BaseRecordElementVariable
    {
        if ($record instanceof File) {
            $this->guid = $record->guid;
            $this->name = $record->file_name;
            $this->title = $record->title;
            $this->mimeType = $record->mime_type;
            $this->size = (int) $record->size;
        }

        return parent::setRecord($record);
    }
}
