<?php

use humhub\components\Migration;

/**
 * Class m241023_070403_rss_content
 */
class m241023_070403_rss_content extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->safeCreateTable('custom_pages_template_rss_content', [
            'id' => $this->primaryKey(),
            'url' => $this->string(1000)->notNull(),
            'cache_time' => $this->integer()->unsigned(),
            'limit' => $this->smallInteger()->unsigned(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->safeDropTable('custom_pages_template_rss_content');
    }
}
