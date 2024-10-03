<?php

use yii\db\Migration;

class m160922_115053_page_url extends Migration
{
    public function up()
    {
        $this->addColumn('custom_pages_page', 'url', $this->string(45));
        #$this->addColumn('custom_pages_container_page','url', $this->string(45));
    }

    public function down()
    {
        echo "m160922_115053_page_url cannot be reverted.\n";

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
