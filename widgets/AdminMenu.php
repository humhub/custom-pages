<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\widgets;

use humhub\helpers\ControllerHelper;
use humhub\modules\admin\permissions\ManageModules;
use humhub\modules\custom_pages\helpers\Url;
use humhub\modules\custom_pages\permissions\ManagePages;
use humhub\modules\ui\menu\MenuLink;
use humhub\modules\ui\menu\widgets\Menu;
use Yii;

/**
 * User Administration Menu
 *
 * @author Basti
 */
class AdminMenu extends Menu
{
    /**
     * @inheritdoc
     */
    public $template = '@humhub/widgets/views/tabMenu';

    public function init()
    {
        if (Yii::$app->user->can([ManageModules::class, ManagePages::class])) {
            $this->addEntry(new MenuLink([
                'label' => Yii::t('CustomPagesModule.base', 'Overview'),
                'url' => Url::toPageOverview(),
                'sortOrder' => 100,
                'isActive' => ControllerHelper::isActivePath('custom_pages', ['page', 'snippet']),
            ]));
        }

        if (Yii::$app->user->can(ManageModules::class)) {
            $this->addEntry(new MenuLink([
                'label' => Yii::t('CustomPagesModule.base', 'Templates'),
                'url' => Url::toTemplateLayoutAdmin(),
                'sortOrder' => 300,
                'isActive' => ControllerHelper::isActivePath('template'),
            ]));

            $this->addEntry(new MenuLink([
                'label' => Yii::t('CustomPagesModule.base', 'Settings'),
                'url' => Url::toModuleConfig(),
                'sortOrder' => 400,
                'isActive' => ControllerHelper::isActivePath('custom_pages', 'config'),
            ]));
        }

        parent::init();
    }

}
