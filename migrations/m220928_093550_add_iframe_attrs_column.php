<?php

use yii\db\Migration;

/**
 * Class m220928_093550_add_iframe_attrs_column
 */
class m220928_093550_add_iframe_attrs_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%custom_pages_page}}', 'iframe_attrs', $this->string(255)->after('page_content'));
        $this->addColumn('{{%custom_pages_snippet}}', 'iframe_attrs', $this->string(255)->after('page_content'));
        $this->addColumn('{{%custom_pages_container_page}}', 'iframe_attrs', $this->string(255)->after('page_content'));
        $this->addColumn('{{%custom_pages_container_snippet}}', 'iframe_attrs', $this->string(255)->after('page_content'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220928_093550_add_iframe_attrs_column cannot be reverted.\n";

        return false;
    }
}
