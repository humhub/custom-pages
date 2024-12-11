<?php

use humhub\components\Migration;

class uninstall extends Migration
{
    public function up()
    {
        $this->safeDropTable('custom_pages_template_container_content_item');
        $this->safeDropTable('custom_pages_template_container_content_template');
        $this->safeDropTable('custom_pages_template_file_content');
        $this->safeDropTable('custom_pages_template_container_content');
        $this->safeDropTable('custom_pages_template_image_content');
        $this->safeDropTable('custom_pages_template_hh_richtext_content');
        $this->safeDropTable('custom_pages_template_richtext_content');
        $this->safeDropTable('custom_pages_template_text_content');
        $this->safeDropTable('custom_pages_template_file_download_content');
        $this->safeDropTable('custom_pages_template_element');
        $this->safeDropTable('custom_pages_template_owner_content');
        $this->safeDropTable('custom_pages_template_instance');
        $this->safeDropTable('custom_pages_template_container_content_definition');
        $this->safeDropTable('custom_pages_template_image_content_definition');
        $this->safeDropTable('custom_pages_template');

        $this->safeDropTable('custom_pages_page');
        $this->safeDropTable('custom_pages_snippet');
        $this->safeDropTable('custom_pages_container_page');
        $this->safeDropTable('custom_pages_container_snippet');
    }

    public function down()
    {
        echo "uninstall does not support migration down.\n";
        return false;
    }

}
