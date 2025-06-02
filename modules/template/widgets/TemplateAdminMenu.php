<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\widgets;

use humhub\helpers\ControllerHelper;
use humhub\modules\ui\menu\MenuLink;
use humhub\modules\ui\menu\widgets\Menu;
use Yii;

/**
 * Template AdminMenu
 */
class TemplateAdminMenu extends Menu
{
    /**
     * @inheritdoc
     */
    public $template = '@humhub/widgets/views/subTabMenu';

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->addEntry(new MenuLink([
            'label' => Yii::t('CustomPagesModule.base', 'Layouts'),
            'url' => ['/custom_pages/template/layout-admin'],
            'sortOrder' => 100,
            'isActive' => ControllerHelper::isActivePath('template', 'layout-admin'),
        ]));

        $this->addEntry(new MenuLink([
            'label' => Yii::t('CustomPagesModule.base', 'Snippet Layouts'),
            'url' => ['/custom_pages/template/snippet-layout-admin'],
            'sortOrder' => 200,
            'isActive' => ControllerHelper::isActivePath('template', 'snippet-layout-admin'),
        ]));

        $this->addEntry(new MenuLink([
            'label' => Yii::t('CustomPagesModule.base', 'Containers'),
            'url' => ['/custom_pages/template/container-admin'],
            'sortOrder' => 300,
            'isActive' => ControllerHelper::isActivePath('template', 'container-admin'),
        ]));

        parent::init();
    }

}
