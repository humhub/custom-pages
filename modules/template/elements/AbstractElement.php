<?php

namespace humhub\modules\custom_pages\modules\template\elements;

use humhub\modules\custom_pages\modules\template\components\TemplateElementValue;
use humhub\modules\custom_pages\modules\template\models\TemplateElement;
use humhub\modules\custom_pages\modules\template\models\TemplateInstance;
use Yii;
use yii\base\BaseObject;

abstract class AbstractElement extends BaseObject
{
    private TemplateElement $element;

    public function __construct(TemplateElement $element)
    {
        if ($element->content_type !== get_class($this)) {
            throw new InvalidArgumentException("Invalid element given!");
        }
    }

    abstract public static function getElementTypeTitle(): string;

    abstract public static function getElementTypeDescription(): string;

    abstract public function getTemplateValue(?TemplateInstance $templateInstance): mixed;

    public static function create(TemplateElement $elementModel)
    {
        return Yii::createObject($elementModel->content_type);
    }

    public function getTemplateName()
    {
        return $this->element->name;
    }

    public function isDynamic(): bool
    {
        return false;
    }

}
