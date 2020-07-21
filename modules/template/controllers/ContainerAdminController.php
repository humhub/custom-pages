<?php

namespace humhub\modules\custom_pages\modules\template\controllers;

use Yii;
use humhub\modules\custom_pages\modules\template\models\Template;

/**
 * Controller for managing container template instaces.
 *
 * @author buddha
 */
class ContainerAdminController extends AdminController
{
    /**
     * @inerhitdoc
     */
    public $type = Template::TYPE_CONTAINER;
    
    /**
     * @inerhitdoc
     */
    public function init() {
        $this->indexHelp = Yii::t('CustomPagesModule.modules_template_controller_ContainerAdminController', 'Here you can manage your template container elements.');
        parent::init();
    }
    
    
}
