<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\widgets;

use humhub\modules\content\helpers\ContentContainerHelper;
use humhub\widgets\BaseMenu;
use Yii;
use yii\helpers\Url;

/**
 * User Administration Menu
 *
 * @author Basti
 */
class ContainerPageMenu extends BaseMenu
{

    public $template = "@humhub/widgets/views/tabMenu";
    public $type = "adminCustomPagesSubNavigation";

    public function init()
    {
        $space = ContentContainerHelper::getCurrent();

        if(!$space) {
            return;
        }
        
        $this->addItem([
            'label' => Yii::t('CustomPagesModule.base', 'Pages'),
            'url' => $space->createUrl('/custom_pages/page/list'),
            'sortOrder' => 100,
            'isActive' => (Yii::$app->controller->id === 'page'),
        ]);  
        
        
        $this->addItem([
            'label' => Yii::t('CustomPagesModule.base', 'Snippets'),
            'url' => $space->createUrl('/custom_pages/snippet/list'),
            'sortOrder' => 200,
            'isActive' => (Yii::$app->controller->id === 'snippet'),
        ]);  
        
        parent::init();
    }

}
