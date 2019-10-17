<?php

namespace tests\codeception\unit\modules\custom_page\template;

use tests\codeception\_support\HumHubDbTestCase;
use Codeception\Specify;
use humhub\modules\custom_pages\modules\template\models\OwnerContent;
use humhub\modules\custom_pages\modules\template\models\TemplateInstance;
use humhub\modules\custom_pages\modules\template\models\Template;
use humhub\modules\custom_pages\modules\template\models\RichtextContent;
use humhub\modules\custom_pages\models\Page;

class TemplateInstanceTest extends HumHubDbTestCase
{

    use Specify;

    public $owner1;
    public $page;

    public function setUp()
    {
        parent::setUp();

        $this->becomeUser('Admin');
    }

    public function testDeleteOwner()
    {
        $owner = new Template([
            'scenario' => 'edit',
            'name' => 'containerTestTmpl',
            'description' => 'My Test Template',
            'type' => Template::TYPE_LAYOUT
        ]);

        $owner->save();

        $page = new Page([
            'type' => '5',
            'title' => 'test2',
            'target' => 'TopMenuWidget',
            'templateId' => $owner->id]);

        $page->save(false);

        $owner2 = new TemplateInstance([
            'object_model' => Page::class,
            'object_id' => $page->id,
            'template_id' => $owner->id
        ]);

        $owner2->save();

        $template = Template::findOne(['id' => 1]);
        $element = $template->getElement('test_content');
        $richtext = new RichtextContent(['content' => 'testContent']);
        $ownerContent = $element->saveInstance($owner2, $richtext);

        $ownerTestContent = $element->getOwnerContent($owner2);

        $content = $ownerContent->instance;

        $this->assertNotNull($content);
        $this->assertEquals($ownerContent->id, $ownerTestContent->id);

        $owner2->delete();

        $this->assertNull(OwnerContent::findOne(['id' => $ownerContent->id]));
        $this->assertNull(RichtextContent::findOne(['id' => $richtext->id]));
    }


    public function testDeleteByOwner()
    {
        $template = Template::findOne(['id' => 1]);
        $element = $template->getElement('test_content');

        $page = new Page([
            'type' => '5',
            'title' => 'test2',
            'target' => 'TopMenuWidget',
            'templateId' => $template->id]);

        $page->save(false);

        $owner = TemplateInstance::findOne(['object_model' => Page::class, 'object_id' => $page->id]);

        $richtext = new RichtextContent(['content' => 'testContent']);
        $ownerContent = $element->saveInstance($owner, $richtext);

        TemplateInstance::deleteByOwner($page);

        $this->assertNull(TemplateInstance::findOne(['id' => $owner->id]));
        $this->assertNull(OwnerContent::findOne(['id' => $ownerContent->id]));
        $this->assertNull(RichtextContent::findOne(['id' => $richtext->id]));
    }

    public function testDeletePage()
    {
        $template = Template::findOne(['id' => 1]);
        $element = $template->getElement('test_content');

        $page = new Page([
            'type' => '5',
            'title' => 'test2',
            'target' => 'TopMenuWidget',
            'templateId' => $template->id]);

        $page->save(false);

        $owner = TemplateInstance::findOne(['object_model' => Page::class, 'object_id' => $page->id]);

        $richtext = new RichtextContent(['content' => 'testContent']);
        $ownerContent = $element->saveInstance($owner, $richtext);

        $this->assertFalse($richtext->isNewRecord);
        $this->assertFalse($ownerContent->isNewRecord);

        $page->delete();

        $this->assertNull(TemplateInstance::findOne(['id' => $owner->id]));
        $this->assertNull(OwnerContent::findOne(['id' => $ownerContent->id]));
        $this->assertNull(RichtextContent::findOne(['id' => $richtext->id]));
    }

    public function testFindByOwner()
    {
        $owner = new Template([
            'scenario' => 'edit',
            'name' => 'containerTestTmpl',
            'description' => 'My Test Template',
            'type' => Template::TYPE_LAYOUT
        ]);

        $owner->save();

        $page = new Page([
            'type' => '5',
            'title' => 'test2',
            'target' => 'TopMenuWidget',
            'templateId' => $owner->id]);

        $page->save(false);

        $owner2 = new TemplateInstance([
            'object_model' => Page::class,
            'object_id' => $page->id,
            'template_id' => $owner->id
        ]);

        $owner2->save();

        $content = new RichtextContent();
        $content->content = '<p>Test</p>';
        $content->save();

        $content2 = new RichtextContent();
        $content2->content = '<p>Test</p>';
        $content2->save();

        $content3 = new RichtextContent();
        $content3->content = '<p>Test</p>';
        $content3->save();

        $instance = new OwnerContent();
        $instance->element_name = 'test';
        $instance->setOwner($owner);
        $instance->setContent($content);
        $instance->save();

        $instance2 = new OwnerContent();
        $instance2->element_name = 'test_2';
        $instance2->setOwner($owner);
        $instance2->setContent($content2);
        $instance2->save();

        $instance3 = new OwnerContent();
        $instance3->element_name = 'test';
        $instance3->setOwner($owner2);
        $instance3->setContent($content3);
        $instance3->save();

        $contentOwner1 = OwnerContent::findByOwner($owner)->all();
        $contentOwner2 = OwnerContent::findByOwner($owner2)->all();

        $this->assertEquals(2, count($contentOwner1));
        $this->assertEquals(1, count($contentOwner2));
    }
}
