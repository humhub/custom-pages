<?php

namespace tests\codeception\unit\modules\custom_page\template;

use humhub\modules\custom_pages\modules\template\models\Template;
use humhub\modules\custom_pages\modules\template\services\TemplateImportService;
use tests\codeception\_support\HumHubDbTestCase;

class TemplateImportTest extends HumHubDbTestCase
{
    public function testImportTemplate()
    {
        $testTemplate = Template::findOne(['name' => 'Test Template']);
        $this->assertNull($testTemplate);

        $service = TemplateImportService::instance();
        $this->assertTrue($service->importFromFile(codecept_data_dir('import/template.json')));
        $this->assertFalse($service->hasErrors());

        $testTemplate = Template::findOne(['name' => 'Test Template']);
        $this->assertInstanceOf(Template::class, $testTemplate);
        $this->assertEquals(2, $testTemplate->getElements()->count());
    }
}
