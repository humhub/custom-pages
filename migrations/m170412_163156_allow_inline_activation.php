<?php

use humhub\components\Migration;

class m170412_163156_allow_inline_activation extends Migration
{
    public function up()
    {
        $this->safeAddColumn('custom_pages_template', 'allow_inline_activation', 'boolean DEFAULT 0');
    }

    public function down()
    {
        echo "m170412_163156_allow_inline_activation cannot be reverted.\n";

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
