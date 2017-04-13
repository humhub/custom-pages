<?php

use yii\db\Migration;

class m170411_124612_fileDownloadContent extends Migration
{
    public function up()
    {
        $this->createTable('custom_pages_template_file_download_content', [
            'id' => 'pk',
            'file_guid' => 'varchar(45) NOT NULL',
            'title' => 'varchar(255) NOT NULL',
            'style' => 'varchar(255) NOT NULL',
            'cssClass' => 'varchar(255) NOT NULL',
            'showFileinfo' => 'boolean DEFAULT 1',
            'showIcon' => 'boolean DEFAULT 1',
        ], '');
    }

    public function down()
    {
        echo "m170411_124612_fileDownloadContent cannot be reverted.\n";

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
