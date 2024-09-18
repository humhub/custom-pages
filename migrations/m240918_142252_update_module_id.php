<?php

use humhub\components\Migration;
use humhub\models\ModuleEnabled;

/**
 * Class m240918_142252_update_module_id
 */
class m240918_142252_update_module_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $moduleEnabled = ModuleEnabled::findOne(['module_id' => 'custom_pages']);
        if ($moduleEnabled) {
            $moduleEnabled->module_id = 'custom-pages';
            $moduleEnabled->save();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240918_142252_update_module_id cannot be reverted.\n";

        return false;
    }
}
