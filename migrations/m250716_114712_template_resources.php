<?php

use humhub\components\Migration;

class m250716_114712_template_resources extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->safeAddColumn('custom_pages_template', 'css', $this->text()->after('source'));
        $this->safeAddColumn('custom_pages_template', 'js', $this->text()->after('css'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250716_114712_template_resources cannot be reverted.\n";

        return false;
    }
}
