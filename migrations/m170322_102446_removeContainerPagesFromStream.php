<?php

use yii\db\Migration;

class m170322_102446_removeContainerPagesFromStream extends Migration
{
    public function up()
    {
        $this->update('content', ['stream_channel' => new yii\db\Expression('NULL')], ['object_model' => 'humhub\modules\custom_pages\models\ContainerPage']);
        $this->update('content', ['stream_channel' => new yii\db\Expression('NULL')], ['object_model' => 'humhub\modules\custom_pages\models\ContainerSnippet']);
    }

    public function down()
    {
        echo "m170322_102446_removeContainerPagesFromStream cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
