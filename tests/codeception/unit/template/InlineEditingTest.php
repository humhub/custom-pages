<?php

namespace tests\codeception\unit\modules\custom_page\template;

use humhub\modules\custom_pages\modules\template\elements\HtmlElement;
use humhub\modules\custom_pages\modules\template\elements\TextElement;
use humhub\modules\custom_pages\modules\template\models\TemplateElement;
use humhub\modules\custom_pages\modules\template\services\TemplateInstanceRendererService;
use tests\codeception\_support\HumHubDbTestCase;

class InlineEditingTest extends HumHubDbTestCase
{
    public function testInlineEditingMarkers()
    {
        // Element 5 is an HTML element of template 3, inline editing is enabled by default for HTML
        $html = HtmlElement::findOne(['id' => 2]);
        $this->assertTrue($html->element->isInlineEditingEnabled());

        $textElement = new TemplateElement(['template_id' => 1, 'name' => 'headline', 'content_type' => TextElement::class]);
        $this->assertTrue($textElement->save());
        // Text may be used inside attributes of the template, so it is disabled by default
        $this->assertFalse($textElement->isInlineEditingEnabled());

        try {
            // Without edit mode nothing is marked
            $this->assertSame('<p>ContainerText</p>', (string) $html->getTemplateVariable());

            TemplateInstanceRendererService::setEditMode();

            // The marker is set on the root tag of the output
            $this->assertSame('<p data-editor-element-id="5">ContainerText</p>', (string) $html->getTemplateVariable());

            $text = new TextElement();
            $text->element_id = $textElement->id;
            $text->content = 'Headline';
            $this->assertSame('Headline', (string) $text->getTemplateVariable());

            // Enabled per element: plain text is wrapped
            $textElement->inline_editing = 1;
            $this->assertTrue($textElement->save());
            $text = new TextElement();
            $text->element_id = $textElement->id;
            $text->content = 'Headline';
            $this->assertSame('<span data-editor-element-id="' . $textElement->id . '">Headline</span>', (string) $text->getTemplateVariable());

            // Empty output is replaced by a placeholder with the element title
            $empty = new TextElement();
            $empty->element_id = $textElement->id;
            $this->assertSame('<span class="cp-editor-element-empty" data-editor-element-id="' . $textElement->id . '">headline</span>', (string) $empty->getTemplateVariable());
        } finally {
            TemplateInstanceRendererService::setEditMode(false);
        }
    }
}
