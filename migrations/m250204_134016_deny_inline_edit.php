<?php

use humhub\components\Migration;

class m250204_134016_deny_inline_edit extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->safeDropColumn('custom_pages_template', 'allow_inline_activation');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250204_134016_deny_inline_edit cannot be reverted.\n";

        return false;
    }
}
