<?php

use humhub\modules\custom_pages\models\ContainerPage;
use yii\db\Migration;

/**
 * Class m190213_135902_align_page_types
 */
class m190213_135903_humhub_richtext_content extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        /**
         * Create Content Type HumHub Richtext
         */
        $this->createTable('custom_pages_template_hh_richtext_content', [
            'id' => 'pk',
            'content' => 'text NOT NULL',
        ], '');
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
