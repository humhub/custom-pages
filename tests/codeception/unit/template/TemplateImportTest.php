<?php

namespace tests\codeception\unit\modules\custom_page\template;

use humhub\modules\custom_pages\modules\template\models\Template;
use humhub\modules\custom_pages\modules\template\models\TemplateElement;
use humhub\modules\custom_pages\modules\template\services\TemplateExportService;
use humhub\modules\custom_pages\modules\template\services\TemplateImportService;
use tests\codeception\_support\HumHubDbTestCase;

class TemplateImportTest extends HumHubDbTestCase
{
    public function testImportTemplateFromFile()
    {
        $testTemplate = Template::findOne(['name' => 'Test Template']);
        $this->assertNull($testTemplate);

        $service = TemplateImportService::instance(Template::TYPE_LAYOUT);
        $this->assertTrue($service->importFromFile(codecept_data_dir('import/template.json')));
        $this->assertFalse($service->hasErrors());

        $testTemplate = Template::findOne(['name' => 'Test Template']);
        $this->assertInstanceOf(Template::class, $testTemplate);
        $this->assertEquals(2, $testTemplate->getElements()->count());

        $expectedElements = TemplateElement::find()
            ->where(['template_id' => $testTemplate->id])
            ->andWhere(['name' => ['test_text', 'test_html']]);
        $this->assertEquals(2, $expectedElements->count());
    }

    public function testCheckWrongJsonData()
    {
        $service = TemplateImportService::instance();
        $service->run([]);
        $this->assertEquals(['Version ' . TemplateExportService::VERSION . ' is required for importing JSON file.'], $service->getErrors());

        $service = TemplateImportService::instance();
        $service->run(['version' => TemplateExportService::VERSION]);
        $this->assertEquals(['Wrong import data!'], $service->getErrors());

        $service = TemplateImportService::instance(Template::TYPE_CONTAINER);
        $service->run([
            'version' => TemplateExportService::VERSION,
            'name' => 'test',
            'type' => Template::TYPE_LAYOUT,
        ]);
        $this->assertEquals(['The template can be imported only as ' . Template::getTypeTitle(Template::TYPE_LAYOUT) . '!'], $service->getErrors());

        // Create default template
        $defaultTemplateData = [
            'version' => TemplateExportService::VERSION,
            'name' => 'test',
            'is_default' => true,
            'type' => Template::TYPE_LAYOUT,
        ];
        $service = TemplateImportService::instance();
        $this->assertTrue($service->run($defaultTemplateData));

        // Try to update default template
        $service = TemplateImportService::instance();
        $service->run($defaultTemplateData);
        $this->assertEquals(['Cannot import default template!'], $service->getErrors());
    }
}
