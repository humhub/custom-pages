<?php

use humhub\components\Migration;

class uninstall extends Migration
{
    public function up()
    {
        $this->safeDropTable('custom_pages_template_container_content_item');
        $this->safeDropTable('custom_pages_template_element');
        $this->safeDropTable('custom_pages_template_element_content');
        $this->safeDropTable('custom_pages_template_element_content_definition');
        $this->safeDropTable('custom_pages_template_owner_content');
        $this->safeDropTable('custom_pages_template_instance');
        $this->safeDropTable('custom_pages_template');
        $this->safeDropTable('custom_pages_page');
    }

    public function down()
    {
        echo "uninstall does not support migration down.\n";
        return false;
    }

}
