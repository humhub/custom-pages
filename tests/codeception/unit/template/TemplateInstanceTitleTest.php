<?php

namespace tests\codeception\unit\modules\custom_page\template;

use humhub\modules\custom_pages\modules\template\elements\FileDownloadElement;
use humhub\modules\custom_pages\modules\template\elements\HtmlElement;
use humhub\modules\custom_pages\modules\template\elements\ImageElement;
use humhub\modules\custom_pages\modules\template\elements\TextElement;
use humhub\modules\custom_pages\modules\template\models\Template;
use humhub\modules\custom_pages\modules\template\models\TemplateElement;
use humhub\modules\custom_pages\modules\template\models\TemplateInstance;
use tests\codeception\_support\HumHubDbTestCase;

class TemplateInstanceTitleTest extends HumHubDbTestCase
{
    public function testElementTitleValues()
    {
        $this->assertTrue(TextElement::supportsTemplateInstanceTitle());
        $this->assertTrue(FileDownloadElement::supportsTemplateInstanceTitle());
        $this->assertTrue(ImageElement::supportsTemplateInstanceTitle());
        $this->assertFalse(HtmlElement::supportsTemplateInstanceTitle());

        $text = new TextElement();
        $text->content = 'Headline';
        $this->assertSame('Headline', $text->getTemplateInstanceTitleValue());

        $download = new FileDownloadElement();
        $this->assertNull($download->getTemplateInstanceTitleValue());
        $download->title = 'Annual report';
        $this->assertSame('Annual report', $download->getTemplateInstanceTitleValue());

        $image = new ImageElement();
        $image->alt = 'Team photo';
        $this->assertSame('Team photo', $image->getTemplateInstanceTitleValue());
    }

    public function testTitleElementValidation()
    {
        $template = $this->createTemplate();
        $this->createElement($template, 'body', HtmlElement::class);
        $this->createElement($template, 'headline', TextElement::class);

        $this->assertSame(['headline' => 'headline (headline)'], $template->getTemplateInstanceTitleElementOptions());

        $template->template_instance_title_element = 'body';
        $this->assertFalse($template->validate(['template_instance_title_element']));

        $template->template_instance_title_element = 'unknown';
        $this->assertFalse($template->validate(['template_instance_title_element']));

        $template->template_instance_title_element = 'headline';
        $this->assertTrue($template->validate(['template_instance_title_element']));

        $template->template_instance_title_element = '';
        $this->assertTrue($template->validate(['template_instance_title_element']));
        $this->assertNull($template->template_instance_title_element);
    }

    public function testDeleteTitleElement()
    {
        $template = $this->createTemplate();
        $headline = $this->createElement($template, 'headline', TextElement::class);
        $template->updateAttributes(['template_instance_title_element' => 'headline']);

        $this->assertNotFalse($headline->delete());

        $template->refresh();
        $this->assertNull($template->template_instance_title_element);
    }

    public function testTemplateInstanceTitle()
    {
        $template = $this->createTemplate();
        $this->createElement($template, 'body', HtmlElement::class);
        $headline = $this->createElement($template, 'headline', TextElement::class);
        $subline = $this->createElement($template, 'subline', TextElement::class);
        $this->createElement($template, 'empty_text', TextElement::class);

        $instance = new TemplateInstance(['template_id' => $template->id, 'page_id' => 1]);
        $this->assertTrue($instance->save());
        $instance->populateRelation('template', $template);

        // Fallback when no suitable element has a value
        $this->assertSame('title_test (' . $instance->id . ')', $instance->getTemplateInstanceTitle());

        // Automatic: the first suitable element with a value
        $this->createContent($instance, $subline, 'Subline');
        $this->assertSame('Subline', $instance->getTemplateInstanceTitle());
        $this->createContent($instance, $headline, 'Headline');
        $this->assertSame('Headline', $instance->getTemplateInstanceTitle());

        // Explicitly selected element
        $template->template_instance_title_element = 'subline';
        $this->assertSame('Subline', $instance->getTemplateInstanceTitle());

        // Explicitly selected element without value falls back, other elements are not used
        $template->template_instance_title_element = 'empty_text';
        $this->assertSame('title_test (' . $instance->id . ')', $instance->getTemplateInstanceTitle());
    }

    private function createTemplate(): Template
    {
        $template = new Template(['name' => 'title_test', 'type' => Template::TYPE_CONTAINER, 'source' => '']);
        $this->assertTrue($template->save());

        return $template;
    }

    private function createElement(Template $template, string $name, string $contentType): TemplateElement
    {
        $element = new TemplateElement(['template_id' => $template->id, 'name' => $name, 'content_type' => $contentType]);
        $element->scenario = TemplateElement::SCENARIO_CREATE;
        $this->assertTrue($element->save());

        return $element;
    }

    private function createContent(TemplateInstance $instance, TemplateElement $element, string $value): void
    {
        $content = new TextElement();
        $content->element_id = $element->id;
        $content->template_instance_id = $instance->id;
        $content->content = $value;
        $this->assertTrue($content->save());
    }
}
