<?php

use yii\db\Migration;

class m180502_084038_logged_in_only extends Migration
{

    public function up()
    {
        $this->addColumn('custom_pages_page', 'logged_in_only', 'boolean DEFAULT 0');
        $this->addColumn('custom_pages_snippet', 'logged_in_only', 'boolean DEFAULT 0');
        $this->addColumn('custom_pages_container_page', 'logged_in_only', 'boolean DEFAULT 0');
        $this->addColumn('custom_pages_container_snippet', 'logged_in_only', 'boolean DEFAULT 0');
    }

    public function down()
    {
        echo "m180502_084038_logged_in_only cannot be reverted.\n";

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
