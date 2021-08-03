<?php

namespace tests\codeception\unit\modules\custom_page\template;

use tests\codeception\_support\HumHubDbTestCase;
use Codeception\Specify;
use humhub\modules\custom_pages\modules\template\models\OwnerContent;
use humhub\modules\custom_pages\modules\template\models\TemplateInstance;


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

       $content = new \humhub\modules\custom_pages\modules\template\models\RichtextContent();
       $content->content = '<p>Test</p>';
       $content->save();

       $pageContent = new OwnerContent();
       $pageContent->setOwner($this->owner);
       $pageContent->setContent($content);
       $pageContent->save();

       $result = $pageContent->render([
           'empty' => false,
           'editMode' => true,
           'element_name' => 'test',
           'owner_model' => get_class($this->owner),
           'owner_id' => $this->owner->id
       ]);

       $this->assertStringContainsString('<p>Test</p>', $result);
       $this->assertStringContainsString('data-template-element="test"', $result);
       $this->assertStringContainsString('data-template-owner="'.get_class($this->owner).'"', $result);
       $this->assertStringContainsString('data-template-content="'. get_class($content) .'"', $result);
       $this->assertStringContainsString('data-template-empty="0"', $result);

    }
}
