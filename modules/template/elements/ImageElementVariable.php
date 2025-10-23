<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\elements;

use humhub\modules\file\models\File;
use yii\db\ActiveRecord;

class ImageElementVariable extends FileElementVariable
{
    public ?string $src;
    public ?string $alt;
    public ?string $height;
    public ?string $width;
    public ?string $style;

    public function __construct(BaseElementContent $elementContent)
    {
        parent::__construct($elementContent);

        /* @var ImageElement $elementContent */
        if ($elementContent instanceof ImageElement) {
            $this->alt = $elementContent->purify($elementContent->alt);
            $this->height = $elementContent->purify($elementContent->definition->height);
            $this->width = $elementContent->purify($elementContent->definition->width);
            $this->style = $elementContent->purify($elementContent->definition->style);
        }
    }

    public function setRecord(?ActiveRecord $record): BaseRecordElementVariable
    {
        if ($record instanceof File) {
            $this->src = $record->getUrl();

            if (!$this->elementContent instanceof ImageElement) {
                // Force to Image Element when this class constructor has been called from different element(e.g. external NewsElement)
                $this->elementContent = new ImageElement(['file_guid' => $record->guid]);
            }
        }

        return parent::setRecord($record);
    }
}
