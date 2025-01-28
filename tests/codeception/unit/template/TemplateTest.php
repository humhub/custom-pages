<?php

namespace tests\codeception\unit\modules\custom_page\template;

use humhub\modules\custom_pages\modules\template\models\Template;
use tests\codeception\_support\HumHubDbTestCase;

class TemplateTest extends HumHubDbTestCase
{
    #use Specify;

    public function testUniqueTemplateName()
    {
        $template = new Template();
        $template->scenario = 'edit';
        $template->name = 'testTemplate';
        $template->description = 'My Test Template';
        $template->type = Template::TYPE_LAYOUT;
        $this->assertFalse($template->save());
    }

    public function testEmptySource()
    {
        $template = new Template();
        $template->scenario = 'source';
        $template->name = 'testTemplate2';
        $template->description = 'My Test Template';
        $template->type = Template::TYPE_LAYOUT;
        $this->assertFalse($template->save());

        $template->source = "Whatever";
        $this->assertTrue($template->save());
    }

    public function testDefaultValues()
    {
        $template = new Template();
        $template->scenario = 'edit';
        $template->name = 'testTemplate2';
        $template->description = 'My Test Template';
        $template->type = Template::TYPE_LAYOUT;
        $this->assertTrue($template->save());

        $template = Template::findOne(['id' => $template->id]);
        $this->assertEquals('0', $template->allow_for_spaces);
    }

    public function testDefaultRender()
    {
        $template = new Template();
        $template->scenario = 'source';
        $template->name = 'testTemplate2';
        $template->description = 'My Test Template';
        $template->source = "Whatever";
        $template->type = Template::TYPE_CONTAINER;
        $template->save();

        $this->assertEquals('Whatever', $template->render());
    }

}
