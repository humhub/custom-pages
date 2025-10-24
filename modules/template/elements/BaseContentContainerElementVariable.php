<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\elements;

use humhub\helpers\Html;
use humhub\modules\content\components\ContentContainerActiveRecord;
use yii\db\ActiveRecord;

class BaseContentContainerElementVariable extends BaseRecordElementVariable
{
    public string $displayName;
    public string $displayNameSub;
    public string $url;
    public string $guid;
    public string $imageUrl;
    public string $bannerImageUrl;
    public array $tags;

    public function setRecord(?ActiveRecord $record): BaseRecordElementVariable
    {
        if ($record instanceof ContentContainerActiveRecord) {
            $this->displayName = $record->displayName;
            $this->displayNameSub = $record->displayNameSub;
            $this->url = $record->getUrl();
            $this->guid = $record->guid;
            $this->imageUrl = $record->profileImage->getUrl();
            $this->bannerImageUrl = $record->profileBannerImage->getUrl();
            $this->tags = $record->tags;
        }

        return parent::setRecord($record);
    }

    public function __toString(): string
    {
        return (string) Html::encode($this->record?->getDisplayName());
    }
}
