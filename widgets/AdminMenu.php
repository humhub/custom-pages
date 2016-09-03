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
            'url' => Url::to(['/custom_pages/admin']),
            'sortOrder' => 100,
            'isActive' => (Yii::$app->controller->module && Yii::$app->controller->module->id == 'custom_pages' 
                    && Yii::$app->controller->id == 'admin'),
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
        
        parent::init();
    }

}
