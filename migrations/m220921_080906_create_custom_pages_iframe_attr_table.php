<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%custom_pages_iframe_attr}}`.
 */
class m220921_080906_create_custom_pages_iframe_attr_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%custom_pages_iframe_attr}}', [
            'id' => $this->primaryKey(),
            'object_model' => $this->string(100)->notNull(),
            'object_id' => $this->integer(11)->notNull(),
            'attr' => $this->string(255),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220921_080906_create_custom_pages_iframe_attr_table cannot be reverted.\n";
    }
}
