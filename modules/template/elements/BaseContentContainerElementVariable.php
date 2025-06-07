<?php

namespace humhub\modules\custom_pages\modules\template\elements;

use humhub\modules\content\components\ContentContainerActiveRecord;

class BaseContentContainerElementVariable extends BaseElementVariable
{
    public string $displayName;
    public string $url;
    public string $guid;

    protected ?ContentContainerActiveRecord $contentContainer = null;

    public function __construct(BaseElementContent $elementContent, string $mode = 'edit')
    {
        parent::__construct($elementContent, $mode);

        if ($elementContent instanceof BaseContentContainerElement) {
            $this->setContentContainer($elementContent->getRecord());
        }
    }

    public function setContentContainer(?ContentContainerActiveRecord $contentContainer): void
    {
        $this->contentContainer = $contentContainer;

        if ($this->contentContainer !== null) {
            $this->displayName = $contentContainer->displayName;
            $this->url = $contentContainer->getUrl();
            $this->guid = $contentContainer->guid;
        }
    }

}