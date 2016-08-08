<?php

namespace humhub\modules\custom_pages\modules\template\controllers;

use Yii;
use humhub\modules\custom_pages\modules\template\models\Template;

/**
 * AdminController
 *
 * @author buddha
 */
class LayoutAdminController extends AdminController
{
    
    public $type = Template::TYPE_LAYOUT;

    
    public function init() {
        $this->indexHelp = Yii::t('CustomPagesModule.modules_template_controller_LayoutAdminController', 'Here you can manage your template layouts.');
    }
    
}
