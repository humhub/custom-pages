<?php

namespace tests\codeception\unit\modules\custom_page\template;

use Codeception\Specify;
use humhub\modules\custom_pages\modules\template\elements\BaseTemplateElementContent;
use humhub\modules\custom_pages\modules\template\elements\RichtextElement;
use humhub\modules\custom_pages\modules\template\models\TemplateInstance;
use humhub\modules\custom_pages\modules\template\models\Template;
use humhub\modules\custom_pages\models\CustomPage;
use tests\codeception\_support\HumHubDbTestCase;

class TemplateInstanceTest extends HumHubDbTestCase
{
    use Specify;

    public $owner1;
    public $page;

    public function setUp(): void
    {
        parent::setUp();

        $this->becomeUser('Admin');
    }

    public function testDeleteTemplateInstance()
    {
        $template = new Template([
            'scenario' => 'edit',
            'name' => 'containerTestTmpl',
            'description' => 'My Test Template',
            'type' => Template::TYPE_LAYOUT,
        ]);
        $template->save();

        $page = new CustomPage([
            'type' => '5',
            'title' => 'test2',
            'target' => 'TopMenuWidget',
            'templateId' => $template->id,
        ]);
        $page->save(false);

        $templateInstance = TemplateInstance::findByOwner($page);

        $template = Template::findOne(['id' => 1]);
        $element = $template->getElement('test_content');
        $richtext = new RichtextElement(['content' => 'testContent']);
        $elementContent = $element->saveInstance($templateInstance, $richtext);

        $this->assertNotNull($elementContent);
        $this->assertEquals($elementContent->id, $richtext->id);

        $templateInstance->delete();

        $this->assertNull(RichtextElement::findOne(['id' => $elementContent->id]));
    }

    public function testDeleteByOwner()
    {
        $template = Template::findOne(['id' => 1]);
        $element = $template->getElement('test_content');

        $page = new CustomPage([
            'type' => '5',
            'title' => 'test2',
            'target' => 'TopMenuWidget',
            'templateId' => $template->id]);
        $page->save(false);

        $owner = TemplateInstance::findByOwner($page);

        $richtext = new RichtextElement(['content' => 'testContent']);
        $elementContent = $element->saveInstance($owner, $richtext);

        TemplateInstance::deleteByOwner($page);

        $this->assertNull(TemplateInstance::findOne(['id' => $owner->id]));
        $this->assertNull(RichtextElement::findOne(['id' => $elementContent->id]));
    }

    public function testDeletePage()
    {
        $template = Template::findOne(['id' => 1]);
        $element = $template->getElement('test_content');

        $page = new CustomPage([
            'type' => '5',
            'title' => 'test2',
            'target' => 'TopMenuWidget',
            'templateId' => $template->id]);
        $page->save(false);

        $owner = TemplateInstance::findByOwner($page);

        $richtext = new RichtextElement(['content' => 'testContent']);
        $elementContent = $element->saveInstance($owner, $richtext);

        $this->assertFalse($richtext->isNewRecord);
        $this->assertFalse($elementContent->isNewRecord);

        $page->hardDelete();

        $this->assertNull(TemplateInstance::findOne(['id' => $owner->id]));
        $this->assertNull(RichtextElement::findOne(['id' => $elementContent->id]));
    }

    public function testFindByOwner()
    {
        $template = Template::findOne(['id' => 1]);
        $element = $template->getElement('test_content');
        $element2 = $template->getElement('test_text');

        $page = new CustomPage([
            'type' => '5',
            'title' => 'test2',
            'target' => 'TopMenuWidget',
            'templateId' => $template->id]);
        $page->save(false);

        $templateInstance = TemplateInstance::findByOwner($page);

        $content = new RichtextElement();
        $content->content = '<p>Test</p>';
        $element->saveInstance($templateInstance, $content);

        $content2 = new RichtextElement();
        $content2->content = '<p>Test</p>';
        $element2->saveInstance($templateInstance, $content2);

        $contents = BaseTemplateElementContent::find()
            ->where(['template_instance_id' => $templateInstance->id]);

        $this->assertEquals(2, $contents->count());
    }
}
