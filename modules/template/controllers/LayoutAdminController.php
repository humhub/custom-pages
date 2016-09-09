<?php

namespace humhub\modules\custom_pages\modules\template\controllers;

use Yii;
use humhub\modules\custom_pages\modules\template\models\Template;

/**
 * Controller for managing layout template instaces.
 *
 * @author buddha
 */
class LayoutAdminController extends AdminController
{
    /**
     * @inheritdoc
     */
    public $type = Template::TYPE_LAYOUT;
    
    /**
     * @inheritdoc
     */
    public function init() {
        $this->indexHelp = Yii::t('CustomPagesModule.modules_template_controller_LayoutAdminController', 
                'Here you can manage your template layouts. Layouts are the root of your template pages and can not be combined with other templates.');
    }
    
}
