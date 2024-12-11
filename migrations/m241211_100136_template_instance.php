<?php

use humhub\components\Migration;

/**
 * Class m241203_100135_records_content
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
        $this->safeDropTable('custom_pages_template_records_content');
    }
}
