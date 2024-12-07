<?php

namespace humhub\modules\custom_pages\modules\template\elements;

use humhub\modules\custom_pages\modules\template\components\TemplateElementValue;
use humhub\modules\custom_pages\modules\template\models\ImageContentDefinition;
use humhub\modules\custom_pages\modules\template\models\TemplateInstance;
use humhub\modules\custom_pages\modules\template\models\TextContent;

class Text extends AbstractElement
{

    public static function getElementTypeTitle(): string
    {
        return "Text element";
    }

    public static function getElementTypeDescription(): string
    {
        return "Use this for plain text elements.";
    }

    public function getTemplateValue(?TemplateInstance $templateInstance): string
    {
        // Switch between default and instance content
        if ($templateInstance && $this->getDataRecord($templateInstance)->hasValues()) {
            $value = $this->getDataRecord($templateInstance)->content;
        } else {
            $value = $this->getDataRecord(null)->content;
        }

        return new TemplateElementValue($this, $value);
    }


    /**
     * @param TemplateInstance|null $templateInstance - Set null for default data
     * @return TextContent
     */
    private function getDataRecord(?TemplateInstance $templateInstance): TextContent
    {
        $data = TextContent::findOne(['template_instance_id' => $templateInstance->id]);
        if ($data === null) {
            $definition = new TextContent();
            $definition->template_instance_id = $templateInstance->id;
        }
    }

    private function getDefinitionRecord(Template $template): ?ImageContentDefinition
    {
        $definition = ImageContentDefinition::findOne(['template_id' => $template->id]);
        if ($definition === null) {
            $definition = new ImageContentDefinition();
            $definition->template_id = $template->id;
        }
        return $definition;
    }


    // More "service" method which are not neccesary be done in the model
}
