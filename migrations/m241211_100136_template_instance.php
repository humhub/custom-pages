<?php

use humhub\components\Migration;

/**
 * Class m241211_100136_template_instance
 */
class m241211_100136_template_instance extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameTable('custom_pages_template_container', 'custom_pages_template_instance');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->renameTable('custom_pages_template_instance', 'custom_pages_template_container');
    }
}
