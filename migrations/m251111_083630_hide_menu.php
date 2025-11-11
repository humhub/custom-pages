<?php

use humhub\components\Migration;

class m251111_083630_hide_menu extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->safeAddColumn('custom_pages_page', 'hide_menu', $this->boolean()->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m251111_083630_hide_menu cannot be reverted.\n";

        return false;
    }
}
