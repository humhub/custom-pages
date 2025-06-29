<?php

namespace tests\codeception\unit\modules\custom_page\template;

use Codeception\Specify;
use humhub\modules\custom_pages\modules\template\elements\HtmlElement;
use humhub\modules\custom_pages\modules\template\models\Template;
use humhub\modules\custom_pages\modules\template\models\TemplateInstance;
use humhub\modules\custom_pages\modules\template\models\TemplateElement;
use humhub\modules\custom_pages\modules\template\services\TemplateInstanceRendererService;
use tests\codeception\_support\HumHubDbTestCase;

class TemplateElementTest extends HumHubDbTestCase
{
    use Specify;

    public $template;
    public $owner;
    public $element;
    public $element2;
    public $defaultContent1;

    public function setUp(): void
    {
        parent::setUp();
        $this->becomeUser('Admin');

        $this->template = Template::findOne(['id' => 1]);
        $this->element = TemplateElement::findOne(['id' => 1]);
        $this->element2 = TemplateElement::findOne(['id' => 2]);

        $this->defaultContent1 = HtmlElement::findOne(['id' => 1]);

        $this->owner = TemplateInstance::findOne(['id' => 1]);
    }

    public function testRenderDefaultContent()
    {
        $result = TemplateInstanceRendererService::instance($this->owner->page, true)->render();

        $this->assertStringContainsString('<p>Default</p>', $result);
        // Edit mode is not allowed for elements except of Container
        $this->assertStringNotContainsString('data-template-element="test_content"', $result);
        $this->assertStringNotContainsString('data-template-empty="0"', $result);
    }

    public function testOverwriteDefaultContent()
    {
        $content = new HtmlElement();
        $content->content = '<p>Non Default</p>';

        $this->element->saveInstance($this->owner, $content);

        $result = TemplateInstanceRendererService::instance($this->owner->page, true)->render();

        $this->assertStringContainsString('<p>Non Default</p>', $result);
        // Edit mode is not allowed for elements except of Container
        $this->assertStringNotContainsString('data-template-element="test_content"', $result);
        $this->assertStringNotContainsString('data-template-empty="0"', $result);
        $this->assertStringNotContainsString('data-template-empty="1"', $result);
    }

    public function testOverwriteEmptyDefaultContent()
    {
        $content = new HtmlElement();
        $content->content = '<p>Non Default2</p>';

        $this->element2->saveInstance($this->owner, $content);

        $result = TemplateInstanceRendererService::instance($this->owner->page, true)->render();

        $this->assertStringContainsString('<p>Non Default2</p>', $result);
        // Edit mode is not allowed for elements except of Container
        $this->assertStringNotContainsString('data-template-element="test_text"', $result);
        $this->assertStringNotContainsString('data-template-empty="1"', $result);
    }

    public function testOverwriteOldContent()
    {
        $content = new HtmlElement();
        $content->content = '<p>Non Default2</p>';
        $this->element2->saveInstance($this->owner, $content);

        $content2 = new HtmlElement();
        $content2->content = '<p>Non Default New</p>';
        $this->element2->saveInstance($this->owner, $content2);

        $result = TemplateInstanceRendererService::instance($this->owner->page, true)->render();

        $this->assertStringContainsString('<p>Non Default New</p>', $result);
        $this->assertNull(HtmlElement::findOne(['id' => $content->id]));
    }

    public function testSaveAsDefaultContent()
    {
        $content = new HtmlElement();
        $content->content = '<p>Default2</p>';
        $this->element->saveAsDefaultContent($content);

        $result = TemplateInstanceRendererService::instance($this->owner->page, true)->render();

        $this->assertStringContainsString('<p>Default2</p>', $result);
        // Get sure the old default content was removed
        $this->assertNull(HtmlElement::findOne(['id' => $this->defaultContent1->id]));
    }

    public function testUniqueTemplateElementName()
    {
        $newElement = new TemplateElement();
        $newElement->scenario = 'create';
        $newElement->name = 'test_content';
        $newElement->content_type = HtmlElement::class;
        $newElement->template_id = $this->template->id;
        $newElement->save();

        $this->assertTrue($newElement->hasErrors());
    }

    public function testGetDefaultContent()
    {
        $this->assertEquals($this->defaultContent1->id, $this->element->getDefaultContent()->getInstance()->id);
    }

    public function testDeleteElement()
    {
        $content = new HtmlElement();
        $content->content = '<p>Non Default</p>';

        $content2 = new HtmlElement();
        $content2->content = '<p>Non Default2</p>';

        $defaultContent = $this->element->getDefaultContent();

        $this->assertNotNull(HtmlElement::findOne(['id' => $defaultContent->id]));

        $this->element->saveInstance($this->owner, $content);
        $this->element2->saveInstance($this->owner, $content2);
        $this->element->delete();

        $this->assertNull(HtmlElement::findOne(['id' => $defaultContent->id]));
        $this->assertNull(HtmlElement::findOne(['id' => $content->id]));

        $this->assertNotNull(HtmlElement::findOne(['id' => $content2->id]));

        $this->assertNull($this->template->getElement('test_content'));
        $this->assertNotNull($this->template->getElement('test_text'));
    }

    public function testDeleteTemplate()
    {
        $this->becomeUser('Admin');
        $content = new HtmlElement();
        $content->content = '<p>Non Default</p>';

        $content2 = new HtmlElement();
        $content2->content = '<p>Non Default2</p>';

        $defaultContent = $this->element->getDefaultContent();

        $this->assertNotNull(HtmlElement::findOne(['id' => $defaultContent->id]));

        $this->element->saveInstance($this->owner, $content);
        $this->element2->saveInstance($this->owner, $content2);

        $this->assertFalse($this->template->delete());

        $this->owner->delete();

        $this->assertEquals('1', $this->template->delete());

        $this->assertNull(HtmlElement::findOne(['id' => $defaultContent->id]));
        $this->assertNull(HtmlElement::findOne(['id' => $content->id]));
    }
}
