<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\widgets;

use Yii;
use yii\helpers\Url;

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
            'label' => Yii::t('CustomPagesModule.base', 'Pages'),
            'url' => Url::to(['/custom_pages/admin/pages']),
            'sortOrder' => 100,
            'isActive' => (Yii::$app->controller->module && Yii::$app->controller->module->id == 'custom_pages' 
                    && Yii::$app->controller->id == 'admin'
                    && Yii::$app->controller->action->id == 'pages')
        ]);
        
        
        $this->addItem([
            'label' => Yii::t('CustomPagesModule.base', 'Snippets'),
            'url' => Url::to(['/custom_pages/snippet']),
            'sortOrder' => 200,
            'isActive' => (Yii::$app->controller->module && Yii::$app->controller->module->id == 'custom_pages' 
                    && Yii::$app->controller->id == 'snippet'),
        ]);  
        
        
        $this->addItem([
            'label' => Yii::t('CustomPagesModule.base', 'Templates'),
            'url' => Url::to(['/custom_pages/template/layout-admin']),
            'sortOrder' => 300,
            
            'isActive' => (Yii::$app->controller->module && Yii::$app->controller->module->id == 'template'),
        ]);

        $this->addItem([
            'label' => Yii::t('CustomPagesModule.base', 'Settings'),
            'url' => Url::to(['/custom_pages/admin/settings']),
            'sortOrder' => 400,
            'isActive' => (Yii::$app->controller->module && Yii::$app->controller->module->id == 'custom_pages'
                && Yii::$app->controller->id == 'admin' && Yii::$app->controller->action->id == 'settings'),
        ]);
        
        parent::init();
    }

}
