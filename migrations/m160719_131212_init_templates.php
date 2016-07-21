<?php

use yii\db\Migration;
use yii\db\Schema;

class m160719_131212_init_templates extends Migration
{
    public function up()
    {
        $this->createTable('custom_pages_template', [
            'id' => 'pk',
            'name' => 'varchar(100) NOT NULL',
            'description' => 'text DEFAULT NULL',
            'source' => 'TEXT NOT NULL',
            'created_at' => 'datetime DEFAULT NULL',
            'created_by' => 'int(11) DEFAULT NULL',
            'updated_at' => 'datetime DEFAULT NULL',
            'updated_by' => 'int(11) DEFAULT NULL',
            'allow_for_spaces' => Schema::TYPE_BOOLEAN. ' DEFAULT 0'
        ], '');
        
        $this->createIndex('unique_tmpl_name', 'custom_pages_template', ['name'], true);
        
        $this->createTable('custom_pages_page_template', [
            'id' => 'pk',
            'page_id' => 'int(11) NOT NULL',
            'template_id' => 'int(11) NOT NULL',
        ], '');
        
        $this->createIndex('unique_page_template', 'custom_pages_page_template', ['page_id'], true);
        $this->addForeignKey('fk-tmpl-page', 'custom_pages_page_template', 'page_id', 'custom_pages_page', 'id', 'CASCADE');
        $this->addForeignKey('fk-tmpl-template', 'custom_pages_page_template', 'template_id', 'custom_pages_template', 'id', 'CASCADE');
        
        $this->createTable('custom_pages_template_content', [
            'id' => 'pk',
            'name' => 'varchar(200) NOT NULL',
            'object_model' => 'varchar(100) NOT NULL',
            'object_id' => 'int(11) NOT NULL'
        ], '');
        
        $this->createTable('custom_pages_template_block', [
            'id' => 'pk',
            'name' => 'varchar(100) NOT NULL',
            'type' => 'varchar(100) NOT NULL',
            'template_id' => 'int(11) DEFAULT NULL',
            'page_template_id' => 'int(11) DEFAULT NULL',
            'template_content_id' => 'int(11) DEFAULT NULL',
        ], '');
        
        $this->addForeignKey('fk-tmpl-block-tmpl', 'custom_pages_template_block', 'template_id', 'custom_pages_template', 'id', 'CASCADE');
        $this->addForeignKey('fk-tmpl-block-page', 'custom_pages_template_block', 'page_template_id', 'custom_pages_page_template', 'id', 'CASCADE');
        $this->addForeignKey('fk-tmpl-block-content', 'custom_pages_template_block', 'template_content_id', 'custom_pages_template_content', 'id', 'CASCADE');
        
        $this->createTable('custom_pages_template_content_html', [
            'id' => 'pk',
            'content' => 'text DEFAULT NULL',
        ], '');
        
    }

    public function down()
    {
        echo "m160719_131212_init_templates cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
