<?php

namespace tests\codeception\unit\modules\custom_page\template;

use Codeception\Specify;
use humhub\modules\custom_pages\modules\template\elements\HtmlElement;
use humhub\modules\custom_pages\modules\template\models\TemplateInstance;
use tests\codeception\_support\HumHubDbTestCase;

class TemplateContentTest extends HumHubDbTestCase
{
    use Specify;

    public $owner;

    public function setUp(): void
    {
        parent::setUp();
        $this->owner = TemplateInstance::findOne(['id' => 1]);
    }

    public function testRenderHtml()
    {
        $content = new HtmlElement();
        $content->element_id = 1;
        $content->content = '<p>Test</p>';
        $content->template_instance_id = $this->owner->id;
        $content->save();

        $result = $content->getTemplateVariable();

        $this->assertStringContainsString('<p>Test</p>', $result);
        // Edit mode is not allowed for elements except of Container
        $this->assertStringNotContainsString('data-template-element="test"', $result);
        $this->assertStringNotContainsString('data-template-empty="0"', $result);
    }
}
