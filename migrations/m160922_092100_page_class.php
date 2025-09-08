<?php

use humhub\components\Migration;

class m160922_092100_page_class extends Migration
{
    public function up()
    {
        $this->safeAddColumn('custom_pages_snippet', 'cssClass', 'varchar(255)');
        $this->safeAddColumn('custom_pages_container_snippet', 'cssClass', 'varchar(255)');
        $this->safeAddColumn('custom_pages_page', 'cssClass', 'varchar(255)');
        $this->safeAddColumn('custom_pages_container_page', 'cssClass', 'varchar(255)');
    }

    public function down()
    {
        echo "m160922_092100_page_class cannot be reverted.\n";

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
