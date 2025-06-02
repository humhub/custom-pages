<?php

namespace tests\codeception\unit\modules\custom_page\template;

use humhub\modules\custom_pages\helpers\PageType;
use humhub\modules\custom_pages\models\CustomPage;
use humhub\modules\custom_pages\modules\template\elements\BaseElementContent;
use humhub\modules\custom_pages\modules\template\elements\ContainerElement;
use humhub\modules\custom_pages\modules\template\models\Template;
use humhub\modules\custom_pages\modules\template\models\TemplateInstance;
use humhub\modules\custom_pages\modules\template\services\TemplateInstanceExportService;
use humhub\modules\custom_pages\modules\template\services\TemplateInstanceImportService;
use humhub\modules\custom_pages\types\TemplateType;
use tests\codeception\_support\HumHubDbTestCase;

class TemplateInstanceImportTest extends HumHubDbTestCase
{
    private ?TemplateInstance $instance = null;

    private function getService(): TemplateInstanceImportService
    {
        if ($this->instance === null) {
            $template = Template::findOne(['name' => 'system_two_column_layout']);
            $this->assertInstanceOf(Template::class, $template);

            $page = new CustomPage();
            $page->type = TemplateType::ID;
            $page->templateId = $template->id;
            $page->title = 'Test page';
            $page->icon = 'fa-adjust';
            $page->target = PageType::TARGET_TOP_MENU;
            $this->assertTrue($page->save());

            $this->instance = TemplateInstance::findByOwner($page);
            $this->assertInstanceOf(TemplateInstance::class, $this->instance);
        }

        return new TemplateInstanceImportService($this->instance);
    }

    public function testImportTemplateInstanceFromFile()
    {
        $this->becomeUser('Admin');

        $service = $this->getService();
        $service->importFromFile(codecept_data_dir('/import/page.json'));
        $this->assertFalse($service->hasErrors());

        $pageInstances = TemplateInstance::find()->where(['page_id' => $this->instance->page_id]);
        $this->assertEquals(3, $pageInstances->count());

        $pageInstanceIds = $pageInstances->select('id')->column();
        $pageElementContents = BaseElementContent::find()->where(['template_instance_id' => $pageInstanceIds]);
        $this->assertEquals(6, $pageElementContents->count());
    }

    public function testCheckWrongJsonData()
    {
        $this->becomeUser('Admin');

        $service = $this->getService();
        $service->run([]);
        $this->assertEquals(['Version ' . TemplateInstanceExportService::VERSION . ' is required for importing JSON file.'], $service->getErrors());

        $service = $this->getService();
        $service->run(['version' => TemplateInstanceExportService::VERSION]);
        $this->assertEquals(['Template is not defined!'], $service->getErrors());

        $service = $this->getService();
        $service->run([
            'version' => TemplateInstanceExportService::VERSION,
            'template' => 'wrong_template_name',
        ]);
        $this->assertEquals(['Template "wrong_template_name" is not found in system!'], $service->getErrors());

        $service = $this->getService();
        $service->run([
            'version' => TemplateInstanceExportService::VERSION,
            'template' => 'system_one_column_layout',
        ]);
        $this->assertEquals(['Template "system_one_column_layout" is not allowed for the selected instance!'], $service->getErrors());

        $service = $this->getService();
        $service->run([
            'version' => TemplateInstanceExportService::VERSION,
            'template' => 'system_two_column_layout',
            'elements' => [],
        ]);
        $this->assertEquals(['Mismatch number of elements for the template "system_two_column_layout"!'], $service->getErrors());

        $service = $this->getService();
        $service->run([
            'version' => TemplateInstanceExportService::VERSION,
            'template' => 'system_two_column_layout',
            'elements' => [
                'content' => [
                    '__element_type' => ContainerElement::class,
                ],
                'wrong' => [],
            ],
        ]);
        $this->assertEquals(['Template "system_two_column_layout" has incompatible or missed elements "sidebar_container"!'], $service->getErrors());
    }
}
