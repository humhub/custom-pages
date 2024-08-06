<?php

use humhub\components\Migration;
use humhub\modules\content\models\Content;
use humhub\modules\custom_pages\models\ContainerPage;
use humhub\modules\custom_pages\models\ContainerSnippet;
use humhub\modules\custom_pages\models\Page;
use humhub\modules\custom_pages\models\Snippet;

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
            ['IN', 'object_model', [Page::class, Snippet::class, ContainerPage::class, ContainerSnippet::class]],
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
