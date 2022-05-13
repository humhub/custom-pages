<?php

use yii\db\Migration;

/**
 * Class m220512_142021_page_allow_attribute
 */
class m220512_142021_page_allow_attribute extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('custom_pages_page','allow_attribute', $this->string(255));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220512_142021_page_allow_attribute cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220512_142021_page_allow_attribute cannot be reverted.\n";

        return false;
    }
    */
}
