<?php

use humhub\modules\custom_pages\models\ContainerPage;
use yii\db\Migration;

/**
 * Class m190213_135902_align_page_types
 */
class m190213_135902_align_page_types extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameColumn('custom_pages_page', 'content', 'page_content');
        $this->renameColumn('custom_pages_snippet', 'content', 'page_content');

        $this->renameColumn('custom_pages_page', 'navigation_class', 'target');
        $this->renameColumn('custom_pages_snippet', 'sidebar', 'target');

        $this->addColumn('custom_pages_container_page', 'target', 'varchar(255) NOT NULL DEFAULT "SpaceMenu"');
        $this->addColumn('custom_pages_container_snippet', 'target', 'varchar(255) NOT NULL DEFAULT "SpaceStreamSidebar"');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190213_135902_align_page_types cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190213_135902_align_page_types cannot be reverted.\n";

        return false;
    }
    */
}
