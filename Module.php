<?php

namespace module\custom_pages;

use module\custom_pages\models\CustomPage;

class Module extends \humhub\components\Module
{

    public $subLayout = "application.modules_core.admin.views._layout";

    public function getConfigUrl()
    {
        return Yii::app()->createUrl('//custom_pages/admin');
    }

    public function disable()
    {
        if (parent::disable()) {

            foreach (CustomPage::find()->all() as $entry) {
                $entry->delete();
            }

            return true;
        }

        return false;
    }

}
