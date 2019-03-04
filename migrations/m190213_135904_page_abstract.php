<?php

use humhub\modules\custom_pages\models\ContainerPage;
use yii\db\Migration;

/**
 * Class m190213_135902_align_page_types
 */
class m190213_135904_page_abstract extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('custom_pages_page', 'abstract', 'TEXT');
        $this->addColumn('custom_pages_container_page', 'abstract', 'TEXT');
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
