<?php

use yii\db\Migration;

class m160831_133950_snippets extends Migration
{

    public function up()
    {
        $this->createTable('custom_pages_snippet', [
            'id' => 'pk',
            'title' => 'varchar(255) NOT NULL',
            'icon' => 'varchar(100)',
            'content' => 'TEXT',
            'type' => 'smallint(6) NOT NULL',
            'sort_order' => 'int(11)',
            'sidebar' => 'varchar(255) NOT NULL',
                ], '');

        $this->createTable('custom_pages_container_snippet', [
            'id' => 'pk',
            'title' => 'varchar(255) NOT NULL',
            'icon' => 'varchar(100)',
            'page_content' => 'TEXT',
            'type' => 'smallint(6) NOT NULL',
            'sort_order' => 'int(11)',
                ], '');
    }

    public function down()
    {
        echo "m160831_133950_snippets cannot be reverted.\n";

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
