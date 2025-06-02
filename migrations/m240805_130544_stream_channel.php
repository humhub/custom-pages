<?php

use humhub\components\Migration;
use humhub\modules\content\models\Content;

/**
 * Class m240805_130544_stream_channel
 */
class m240805_130544_stream_channel extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Content::updateAll(
            ['stream_channel' => 'default'],
            ['IN', 'object_model', [
                'humhub\\modules\\custom_pages\\models\\Page',
                'humhub\\modules\\custom_pages\\models\\Snippet',
                'humhub\\modules\\custom_pages\\models\\ContainerPage',
                'humhub\\modules\\custom_pages\\models\\ContainerSnippet',
            ]],
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240805_130544_stream_channel cannot be reverted.\n";

        return false;
    }
}
