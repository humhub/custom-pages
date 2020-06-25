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

    public function setUp()
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

       $this->assertContains('<p>Test</p>', $result);
       $this->assertContains('data-template-element="test"', $result);
       $this->assertContains('data-template-owner="'.get_class($this->owner).'"', $result);
       $this->assertContains('data-template-content="'. get_class($content) .'"', $result);
       $this->assertContains('data-template-empty="0"', $result);

    }
}
