<?php

use humhub\components\Migration;

class m150917_104409_add_new_windows extends Migration
{
    public function up()
    {
        $this->safeAddColumn('custom_pages_page', 'in_new_window', 'boolean DEFAULT 0');
        $this->safeAddColumn('custom_pages_container_page', 'in_new_window', 'boolean DEFAULT 0');
    }

    public function down()
    {
        echo "m150917_104409_add_new_windows cannot be reverted.\n";

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
