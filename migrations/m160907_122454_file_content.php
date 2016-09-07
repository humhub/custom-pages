<?php

use yii\db\Migration;


class m160907_122454_file_content extends Migration
{
    public function up()
    {
        /**
         * Create Content Type File
         */
        $this->createTable('custom_pages_template_file_content', [
            'id' => 'pk',
            'file_guid' => 'varchar(45) NOT NULL',
        ], '');
    }
    
    

    public function down()
    {
        echo "m160907_122454_file_content cannot be reverted.\n";

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
