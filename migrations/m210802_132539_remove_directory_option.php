<?php

use humhub\modules\custom_pages\models\Page;
use humhub\modules\custom_pages\models\Snippet;
use yii\db\Migration;

/**
 * Class m210802_132539_remove_directory_option
 */
class m210802_132539_remove_directory_option extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('UPDATE custom_pages_page SET target = :newTarget WHERE target = :oldTarget', [
            ':newTarget' => Page::NAV_CLASS_EMPTY,
            ':oldTarget' => 'DirectoryMenu',
        ]);

        $this->execute('UPDATE custom_pages_snippet SET target = :newTarget WHERE target = :oldTarget', [
            ':newTarget' => Snippet::SIDEBAR_DASHBOARD,
            ':oldTarget' => 'Directory',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210802_132539_remove_directory_option cannot be reverted.\n";

        return false;
    }
}
