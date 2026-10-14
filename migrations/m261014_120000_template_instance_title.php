<?php

use humhub\components\Migration;

class m261014_120000_template_instance_title extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Name of the element used as title of the template instances, NULL means the first suitable element
        $this->safeAddColumn('custom_pages_template', 'template_instance_title_element', $this->string(100)->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('custom_pages_template', 'template_instance_title_element');
    }
}
