<?php

use yii\db\Migration;

class m160908_084038_admin_only_other_pagetypes extends Migration
{

    public function up()
    {
        $this->addColumn('custom_pages_snippet', 'admin_only', 'boolean DEFAULT 0');
        $this->addColumn('custom_pages_container_page', 'admin_only', 'boolean DEFAULT 0');
        $this->addColumn('custom_pages_container_snippet', 'admin_only', 'boolean DEFAULT 0');
    }

    public function down()
    {
        echo "m160908_084038_admin_only_other_pagetypes cannot be reverted.\n";

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
