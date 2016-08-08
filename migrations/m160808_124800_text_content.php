<?php

use yii\db\Migration;

class m160808_124800_text_content extends Migration
{
    public function up()
    {
        $this->createTable('custom_pages_template_text_content', [
            'id' => 'pk',
            'content' => 'text NOT NULL',
        ], '');
    }

    public function down()
    {
        echo "m160808_124800_text_content cannot be reverted.\n";

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
