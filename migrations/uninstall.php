<?php

use humhub\components\Migration;

class uninstall extends Migration
{
    public function up()
    {
        $this->safeDropForeignKey('fk-tmpl-container-item-element-content', 'custom_pages_template_element_container_item');
        $this->safeDropForeignKey('fk-container_item_id', 'custom_pages_template_instance');
        $this->safeDropForeignKey('fk-element_id', 'custom_pages_template_element_content');

        $this->safeDropTable('custom_pages_template_element_container_item');
        $this->safeDropTable('custom_pages_template_element');
        $this->safeDropTable('custom_pages_template_element_content');
        $this->safeDropTable('custom_pages_template_element_content_definition');
        $this->safeDropTable('custom_pages_template_instance');
        $this->safeDropTable('custom_pages_template');
        $this->safeDropTable('custom_pages_page_setting');
        $this->safeDropTable('custom_pages_page');
    }

    public function down()
    {
        echo "uninstall does not support migration down.\n";
        return false;
    }

}
