<?php

use humhub\components\Migration;

class m160817_130334_page_content_medium_text extends Migration
{
    public function up()
    {
        $table = 'custom_pages_page';
        $column = 'content';
        if ($this->columnExists($column, $table)) {
            $this->alterColumn($table, $column, 'MEDIUMTEXT');
        }
    }

    public function down()
    {
        echo "m160817_130334_page_content_medium_text cannot be reverted.\n";

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
