<?php

use humhub\components\Migration;

class m141026_135537_adminOnly extends Migration
{

    public function up()
    {
        $this->addColumn('custom_pages_page', 'admin_only', 'boolean DEFAULT 0');
    }

    public function down()
    {
        echo "m141026_135537_adminOnly does not support migration down.\n";
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
