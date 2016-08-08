<?php

namespace tests\codeception\unit\modules\custom_page\template;

use tests\codeception\_support\HumHubDbTestCase;
use Codeception\Specify;
use humhub\modules\custom_pages\modules\template\models\OwnerContent;
use humhub\modules\custom_pages\modules\template\models\TemplateInstance;
use humhub\modules\custom_pages\modules\template\models\Template;

class TemplateInstanceTest extends HumHubDbTestCase
{

    use Specify;

    public $owner2;
    public $owner1;

    public function setUp()
    {
        parent::setUp();
        $this->owner1 = new Template();
        $this->owner1->scenario = 'edit';
        $this->owner1->name = 'containerTestTmpl';
        $this->owner1->description = 'My Test Template';
        $this->owner1->save();
        
        $this->owner2 = new TemplateInstance();
        $this->owner2->object_model = \humhub\modules\custom_pages\models\Page::className();
        $this->owner2->object_id = 2;
        $this->owner2->template_id = $this->owner1->id;
        $this->owner2->save();
    }

    public function testFindByOwner()
    {
        $content = new \humhub\modules\custom_pages\modules\template\models\RichtextContent();
        $content->content = '<p>Test</p>';
        $content->save();

        $content2 = new \humhub\modules\custom_pages\modules\template\models\RichtextContent();
        $content2->content = '<p>Test</p>';
        $content2->save();

        $content3 = new \humhub\modules\custom_pages\modules\template\models\RichtextContent();
        $content3->content = '<p>Test</p>';
        $content3->save();

        $instance = new OwnerContent();
        $instance->element_name = 'test';
        $instance->setOwner($this->owner1);
        $instance->setContent($content);
        $instance->save();

        $instance2 = new OwnerContent();
        $instance2->element_name = 'test_2';
        $instance2->setOwner($this->owner1);
        $instance2->setContent($content2);
        $instance2->save();

        $instance3 = new OwnerContent();
        $instance3->element_name = 'test';
        $instance3->setOwner($this->owner2);
        $instance3->setContent($content3);
        $instance3->save();

        $contentOwner1 = OwnerContent::findByOwner($this->owner1)->all();
        $contentOwner2 = OwnerContent::findByOwner($this->owner2)->all();
    
        $this->assertEquals(2, count($contentOwner1));
        $this->assertEquals(1, count($contentOwner2));
    }
}
