<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\elements;

use humhub\modules\content\components\ContentContainerActiveRecord;

class BaseContentContainerElementVariable extends BaseElementVariable
{
    protected ?ContentContainerActiveRecord $contentContainer = null;

    public function setContentContainer(?ContentContainerActiveRecord $contentContainer): void
    {
        $this->contentContainer = $contentContainer;
    }

    public function __isset($name): bool
    {
        return property_exists($this, $name) || isset($this->contentContainer->$name);
    }

    public function __get($name)
    {
        return $this->$name ?? $this->contentContainer->$name ?? null;
    }
}
