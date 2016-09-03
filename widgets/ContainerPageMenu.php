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
class ContainerPageMenu extends \humhub\widgets\BaseMenu
{

    public $template = "@humhub/widgets/views/tabMenu";
    public $type = "adminCustomPagesSubNavigation";

    public function init()
    {
        $space = Yii::$app->controller->contentContainer;
        
        $this->addItem([
            'label' => Yii::t('CustomPagesModule.base', 'Pages'),
            'url' => $space->createUrl('/custom_pages/container/list'),
            'sortOrder' => 100,
            'isActive' => (Yii::$app->controller->id == 'container'),
        ]);  
        
        
        $this->addItem([
            'label' => Yii::t('CustomPagesModule.base', 'Snippets'),
            'url' => $space->createUrl('/custom_pages/container-snippet/list'),
            'sortOrder' => 200,
            'isActive' => (Yii::$app->controller->id == 'container-snippet'),
        ]);  
        
        parent::init();
    }

}
