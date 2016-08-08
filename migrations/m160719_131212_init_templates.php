<?php

use yii\db\Migration;
use yii\db\Schema;

class m160719_131212_init_templates extends Migration
{
    public function up()
    {
        /**
         * Create Template Table
         */
        $this->createTable('custom_pages_template', [
            'id' => 'pk',
            'name' => 'varchar(100) NOT NULL',
            'engine' => 'varchar(100) NOT NULL',
            'description' => 'text DEFAULT NULL',
            'source' => 'TEXT DEFAULT NULL',
            'allow_for_spaces' => Schema::TYPE_BOOLEAN. ' DEFAULT 0',
            'type' => 'varchar(100) NOT NULL',
            'created_at' => 'datetime DEFAULT NULL',
            'created_by' => 'int(11) DEFAULT NULL',
            'updated_at' => 'datetime DEFAULT NULL',
            'updated_by' => 'int(11) DEFAULT NULL'
        ], '');
        
        $this->createIndex('unique_tmpl_name', 'custom_pages_template', ['name'], true);
        
        /**
         * Create TemplateInstance Table
         */
        $this->createTable('custom_pages_template_container', [
            'id' => 'pk',
            'object_model' => 'varchar(100) NOT NULL',
            'object_id' => 'int(11) NOT NULL',
            'template_id' => 'int(11) NOT NULL',
        ], '');
        
        $this->addForeignKey('fk-tmpl-template', 'custom_pages_template_container', 'template_id', 'custom_pages_template', 'id', 'CASCADE');
        
        /**
         * Create OwnerContent Table
         */
        $this->createTable('custom_pages_template_owner_content', [
            'id' => 'pk',
            'element_name' => 'varchar(100) NOT NULL',
            'owner_model' => 'varchar(100) NOT NULL',
            'owner_id' => 'int(11) NOT NULL',
            'content_type' => 'varchar(100) NOT NULL',
            'content_id' => 'int(11) NOT NULL',
            'use_default' => Schema::TYPE_BOOLEAN. ' DEFAULT 0'
        ], '');
        
        /**
         * Create TemplateElementDefinition
         */
        $this->createTable('custom_pages_template_element', [
            'id' => 'pk',
            'template_id' => 'int(11) DEFAULT NULL',
            'name' => 'varchar(100) NOT NULL',
            'content_type' => 'varchar(100) NOT NULL'
        ], '');
        
        $this->addForeignKey('fk-tmpl-element-tmpl', 'custom_pages_template_element', 'template_id', 'custom_pages_template', 'id', 'CASCADE');
        
        /**
         * Create table for ImageContentDefinition
         */
        $this->createTable('custom_pages_template_image_content_definition', [
            'id' => 'pk',
            'height' => 'int(10) DEFAULT NULL',
            'width' => 'int(10) DEFAULT NULL',
            'style' => 'varchar(200) DEFAULT NULL',
            'is_default' => Schema::TYPE_BOOLEAN. ' DEFAULT 0'
        ], '');
        
        /**
         * Create Content Type Image
         */
        $this->createTable('custom_pages_template_image_content', [
            'id' => 'pk',
            'file_guid' => 'varchar(45) NOT NULL',
            'alt' => 'varchar(100) DEFAULT NULL',
            'definition_id' => 'int(11) DEFAULT NULL',
        ], '');
        
        $this->addForeignKey('fk-tmpl-image-definition', 'custom_pages_template_image_content', 'definition_id', 'custom_pages_template_image_content_definition', 'id', 'CASCADE');
        
        
        /**
         * Create Content Type HTML
         */
        $this->createTable('custom_pages_template_richtext_content', [
            'id' => 'pk',
            'content' => 'text NOT NULL',
        ], '');
        
        /**
         * Create table for ContainerContentDefinition
         */
        $this->createTable('custom_pages_template_container_content_definition', [
            'id' => 'pk',
            'allow_multiple' => Schema::TYPE_BOOLEAN. ' DEFAULT 0',
            'is_inline' => Schema::TYPE_BOOLEAN. ' DEFAULT 0',
            'is_default' => Schema::TYPE_BOOLEAN. ' DEFAULT 0'
        ], '');
        
        /**
         * Create table for ContainerContent
         */
        $this->createTable('custom_pages_template_container_content', [
            'id' => 'pk',
            'definition_id' => 'int(11) DEFAULT NULL',
        ], '');
        
        $this->addForeignKey('fk-tmpl-container-definition', 'custom_pages_template_container_content', 'definition_id', 'custom_pages_template_container_content_definition', 'id', 'CASCADE');
        
        $this->createTable('custom_pages_template_container_content_template', [
            'id' => 'pk',
            'template_id' => 'int(11) NOT NULL',
            'definition_id' => 'int(11) NOT NULL'
        ], '');
        
        $this->addForeignKey('fk-tmpl-container-tmpl', 'custom_pages_template_container_content_template', 'template_id', 'custom_pages_template', 'id', 'CASCADE');
        $this->addForeignKey('fk-tmpl-container-tmpl-definition', 'custom_pages_template_container_content_template', 'definition_id', 'custom_pages_template_container_content_definition', 'id', 'CASCADE');
        
        $this->createTable('custom_pages_template_container_content_item', [
            'id' => 'pk',
            'template_id' => 'int(11) NOT NULL',
            'container_content_id' => 'int(11) NOT NULL',
            'sort_order' => "int(11) DEFAULT '100'",
            'title' => 'varchar(100) DEFAULT NULL'
        ], '');
        
        $this->addForeignKey('fk-tmpl-container-item-tmpl', 'custom_pages_template_container_content_item', 'template_id', 'custom_pages_template', 'id', 'CASCADE');
        $this->addForeignKey('fk-tmpl-container-item-content', 'custom_pages_template_container_content_item', 'container_content_id', 'custom_pages_template_container_content', 'id', 'CASCADE');
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
