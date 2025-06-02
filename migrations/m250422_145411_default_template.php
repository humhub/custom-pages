<?php

use humhub\components\Migration;
use humhub\modules\custom_pages\modules\template\services\TemplateImportService;

class m250422_145411_default_template extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->safeAddColumn('custom_pages_template', 'is_default', $this->boolean()->notNull()->defaultValue(0));
        TemplateImportService::instance()->importDefaultTemplates();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->safeDropColumn('custom_pages_template', 'is_default');
    }
}
