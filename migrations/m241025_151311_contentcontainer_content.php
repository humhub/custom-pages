<?php

use humhub\components\Migration;

/**
 * Class m241025_151311_contentcontainer_content
 */
class m241025_151311_contentcontainer_content extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->safeCreateTable('custom_pages_template_contentcontainer_content', [
            'id' => $this->primaryKey(),
            'guid' => $this->string(36)->null(),
            'class' => $this->string(60)->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->safeDropTable('custom_pages_template_contentcontainer_content');
    }
}
