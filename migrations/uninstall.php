<?php

use yii\db\Migration;

class uninstall extends Migration
{

    public function up()
    {
        $this->dropTable('custom_pages_template_container_content_item');
        $this->dropTable('custom_pages_template_container_content_template');
        $this->dropTable('custom_pages_template_file_content');
        $this->dropTable('custom_pages_template_container_content');
        $this->dropTable('custom_pages_template_image_content');
        $this->dropTable('custom_pages_template_richtext_content');
        $this->dropTable('custom_pages_template_text_content');
        $this->dropTable('custom_pages_template_file_download_content');
        $this->dropTable('custom_pages_template_element');
        $this->dropTable('custom_pages_template_owner_content');
        $this->dropTable('custom_pages_template_container');
        $this->dropTable('custom_pages_template_container_content_definition');
        $this->dropTable('custom_pages_template_image_content_definition');
        $this->dropTable('custom_pages_template');
        
        $this->dropTable('custom_pages_page');
        $this->dropTable('custom_pages_container_page');
        
        $this->dropTable('custom_pages_container_snippet');
        $this->dropTable('custom_pages_snippet');
        
    }

    public function down()
    {
        echo "uninstall does not support migration down.\n";
        return false;
    }

}
