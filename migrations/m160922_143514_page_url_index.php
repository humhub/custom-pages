<?php

use yii\db\Migration;

class m160922_143514_page_url_index extends Migration
{
    public function up()
    {
        $this->createIndex('custom-page-url-unique', 'custom_pages_page', 'url', false);
    }

    public function down()
    {
        echo "m160922_143514_page_url_index cannot be reverted.\n";

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
