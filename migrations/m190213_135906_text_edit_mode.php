<?php

use humhub\components\Migration;
use humhub\modules\custom_pages\modules\template\models\ContainerContent;
use humhub\modules\custom_pages\modules\template\models\Template;
use yii\db\Schema;

/**
 * Class m190213_135902_align_page_types
 */
class m190213_135906_text_edit_mode extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('custom_pages_template_text_content', 'inline_text',  Schema::TYPE_BOOLEAN. ' DEFAULT 1');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190213_135902_align_page_types cannot be reverted.\n";

        return false;
    }
}
