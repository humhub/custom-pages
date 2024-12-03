<?php

use humhub\components\Migration;

/**
 * Class m241203_100135_records_content
 */
class m241203_100135_records_content extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->safeCreateTable('custom_pages_template_records_content', [
            'id' => $this->primaryKey(),
            'class' => $this->string(127)->notNull(),
            'type' => $this->string(15)->null(),
            'options' => $this->text()->null(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->safeDropTable('custom_pages_template_records_content');
    }
}
