<?php

use yii\db\Migration;

class uninstall extends Migration
{

    public function up()
    {
        $this->dropTable('custom_pages_template_content_html');
        $this->dropTable('custom_pages_template_block');
        $this->dropTable('custom_pages_template_content');
        $this->dropTable('custom_pages_page_template');
        $this->dropTable('custom_pages_template');
        
        $this->dropTable('custom_pages_page');
        $this->dropTable('custom_pages_container_page');
    }

    public function down()
    {
        echo "uninstall does not support migration down.\n";
        return false;
    }

}
