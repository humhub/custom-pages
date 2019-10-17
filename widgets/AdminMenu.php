<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\widgets;

use humhub\modules\custom_pages\helpers\Url;
use Yii;

/**
 * User Administration Menu
 *
 * @author Basti
 */
class AdminMenu extends \humhub\widgets\BaseMenu
{

    public $template = "@humhub/widgets/views/tabMenu";
    public $type = "adminCustomPagesSubNavigation";

    public function init()
    {
        $this->addItem([
            'label' => Yii::t('CustomPagesModule.base', 'Overview'),
            'url' => Url::toPageOverview(),
            'sortOrder' => 100,
            'isActive' => (Yii::$app->controller->module && Yii::$app->controller->module->id == 'custom_pages' 
                    && Yii::$app->controller->id == 'page')
        ]);
        
        
        $this->addItem([
            'label' => Yii::t('CustomPagesModule.base', 'Templates'),
            'url' => Url::toTemplateLayoutAdmin(),
            'sortOrder' => 300,
            'isActive' => (Yii::$app->controller->module && Yii::$app->controller->module->id == 'template'),
        ]);

        $this->addItem([
            'label' => Yii::t('CustomPagesModule.base', 'Settings'),
            'url' => Url::toModuleConfig(),
            'sortOrder' => 400,
            'isActive' => (Yii::$app->controller->module && Yii::$app->controller->module->id == 'custom_pages'
                && Yii::$app->controller->id == 'config'),
        ]);
        
        parent::init();
    }

}
