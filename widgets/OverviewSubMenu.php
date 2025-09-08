<?php

/**
 * Created by PhpStorm.
 * User: kingb
 * Date: 15.02.2019
 * Time: 13:23
 */

namespace humhub\modules\custom_pages\widgets;

use humhub\helpers\ControllerHelper;
use humhub\modules\content\helpers\ContentContainerHelper;
use humhub\modules\custom_pages\helpers\Url;
use humhub\modules\ui\menu\MenuLink;
use humhub\modules\ui\menu\widgets\Menu;
use Yii;

class OverviewSubMenu extends Menu
{
    /**
     * @inheritdoc
     */
    public $template = '@humhub/widgets/views/subTabMenu';

    public function init()
    {
        $container = ContentContainerHelper::getCurrent();

        $this->addEntry(new MenuLink([
            'label' => Yii::t('CustomPagesModule.base', 'Pages'),
            'url' => Url::toPageOverview($container),
            'sortOrder' => 100,
            'isActive' => ControllerHelper::isActivePath('custom_pages', 'page'),
        ]));

        $this->addEntry(new MenuLink([
            'label' => Yii::t('CustomPagesModule.base', 'Snippets'),
            'url' => Url::toSnippetOverview($container),
            'sortOrder' => 200,
            'isActive' => ControllerHelper::isActivePath('custom_pages', 'snippet'),
        ]));

        parent::init();
    }

}
