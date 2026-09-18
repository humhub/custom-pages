<?php

use humhub\components\Migration;

class m260909_180000_inline_editing extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // NULL means the default of the element type is used
        $this->safeAddColumn('custom_pages_template_element', 'inline_editing', $this->boolean()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('custom_pages_template_element', 'inline_editing');
    }
}
