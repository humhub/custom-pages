<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\widgets;

use humhub\modules\admin\permissions\ManageModules;
use humhub\modules\custom_pages\helpers\Url;
use humhub\modules\custom_pages\permissions\ManagePages;
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
        if (Yii::$app->user->can([ManageModules::class, ManagePages::class])) {
            $this->addItem([
                'label' => Yii::t('CustomPagesModule.base', 'Overview'),
                'url' => Url::toPageOverview(),
                'sortOrder' => 100,
                'isActive' => (Yii::$app->controller->module && Yii::$app->controller->module->id == 'custom-pages'
                        && Yii::$app->controller->id == 'page')
            ]);
        }

        if (Yii::$app->user->can(ManageModules::class)) {
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
                'isActive' => (Yii::$app->controller->module && Yii::$app->controller->module->id == 'custom-pages'
                        && Yii::$app->controller->id == 'config'),
            ]);
        }
        
        parent::init();
    }

}
