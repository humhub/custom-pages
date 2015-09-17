<?php

use yii\db\Schema;
use yii\db\Migration;

class m150916_131805_container extends Migration
{

    public function up()
    {
        $this->createTable('custom_pages_container_page', array(
            'id' => 'pk',
            'title' => 'varchar(255) NOT NULL',
            'icon' => 'varchar(100)',
            'page_content' => 'TEXT',
            'type' => 'smallint(6) NOT NULL',
            'sort_order' => 'int(11)',
                ), '');
    }

    public function down()
    {
        echo "m150916_131805_container cannot be reverted.\n";

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
