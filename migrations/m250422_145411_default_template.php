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

        // Need to add the columns here to avoid errors on work with class Template
        $this->safeAddColumn('custom_pages_template', 'css', $this->text()->after('source'));
        $this->safeAddColumn('custom_pages_template', 'js', $this->text()->after('css'));

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
