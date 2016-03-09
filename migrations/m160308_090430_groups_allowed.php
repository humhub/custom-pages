<?php

use  \humhub\components\Migration;

class m160308_090430_groups_allowed extends Migration
{
    public function up()
    {
        $this->addColumn('custom_pages_page', 'groups_allowed', 'string');
    }

    public function down()
    {
        echo "m160308_090430_groups_allowed cannot be reverted.\n";

        return false;
    }
}