<?php

use humhub\components\Migration;

class m251007_145322_setting_primary_key extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->safeAddColumn('custom_pages_page_setting', 'id', $this->primaryKey()->first());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m251007_145322_setting_primary_key cannot be reverted.\n";

        return false;
    }
}
