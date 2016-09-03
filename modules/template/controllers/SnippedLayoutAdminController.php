<?php

namespace humhub\modules\custom_pages\modules\template\controllers;

use Yii;
use humhub\modules\custom_pages\modules\template\models\Template;

/**
 * AdminController
 *
 * @author buddha
 */
class SnippedLayoutAdminController extends AdminController
{
    
    public $type = Template::TYPE_SNIPPED_LAYOUT;

    
    public function init() {
        $this->indexHelp = Yii::t('CustomPagesModule.modules_template_controller_SnippedTemplateAdminController', 
                'Here you can manage your snipped layouts. Snippet layouts are templates, which can be included into sidebars.');
    }
    
}
