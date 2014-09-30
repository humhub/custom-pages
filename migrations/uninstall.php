<?php

class uninstall extends ZDbMigration
{

    public function up()
    {

        $this->dropTable('custom_pages_page');
    }

    public function down()
    {
        echo "uninstall does not support migration down.\n";
        return false;
    }

}
