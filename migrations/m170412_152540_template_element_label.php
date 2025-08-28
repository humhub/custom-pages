<?php

use humhub\components\Migration;

class m170412_152540_template_element_label extends Migration
{
    public function up()
    {
        $this->safeAddColumn('custom_pages_template_element', 'title', 'varchar(255) DEFAULT NULL');
    }

    public function down()
    {
        echo "m170412_152540_template_element_label cannot be reverted.\n";

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
