<?php

use humhub\components\Migration;

class m140930_045035_initial extends Migration
{

    public function up()
    {
        $this->createTable('custom_pages_page', array(
            'id' => 'pk',
            'type' => 'smallint(6) NOT NULL',
            'title' => 'varchar(255) NOT NULL',
            'icon' => 'varchar(100)',
            'content' => 'TEXT',
            'sort_order' => 'int(11)',
            'navigation_class' => 'varchar(255) NOT NULL',
                ), '');
    }

    public function down()
    {
        echo "m140930_045035_initial does not support migration down.\n";
        return false;
    }

    /*
      // Use safeUp/safeDown to do migration with transaction
      public function safeUp()
      {
      }

      public function safeDown()
      {
      }
     */
}
