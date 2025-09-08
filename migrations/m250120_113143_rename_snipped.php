<?php

use humhub\components\Migration;

class m250120_113143_rename_snipped extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->update('custom_pages_template', ['type' => 'snippet-layout'], ['type' => 'snipped-layout']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250120_113143_rename_snipped cannot be reverted.\n";

        return false;
    }
}
