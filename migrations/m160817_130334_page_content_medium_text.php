<?php

use yii\db\Migration;

class m160817_130334_page_content_medium_text extends Migration
{
    public function up()
    {
        $this->alterColumn('custom_pages_page', 'content', 'MEDIUMTEXT');
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
