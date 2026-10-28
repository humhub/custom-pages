<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\widgets;

use humhub\helpers\ControllerHelper;
use humhub\modules\admin\permissions\ManageModules;
use humhub\modules\content\helpers\ContentContainerHelper;
use humhub\modules\custom_pages\helpers\Url;
use humhub\modules\custom_pages\permissions\ManagePages;
use humhub\widgets\menu\MenuLink;
use humhub\widgets\menu\PillTabs;
use Yii;

/**
 * The tabs of the Custom Pages page header: Pages and Snippets, in the administration also
 * Templates and Settings.
 */
class AdminMenu extends PillTabs
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $container = ContentContainerHelper::getCurrent();
        $canManagePages = $container !== null || Yii::$app->user->can([ManageModules::class, ManagePages::class]);

        $this->addEntry(new MenuLink([
            'label' => Yii::t('CustomPagesModule.base', 'Pages'),
            'url' => Url::toPageOverview($container),
            'sortOrder' => 100,
            'isActive' => ControllerHelper::isActivePath('custom_pages', 'page'),
            'isVisible' => $canManagePages,
        ]));

        $this->addEntry(new MenuLink([
            'label' => Yii::t('CustomPagesModule.base', 'Snippets'),
            'url' => Url::toSnippetOverview($container),
            'sortOrder' => 200,
            'isActive' => ControllerHelper::isActivePath('custom_pages', 'snippet'),
            'isVisible' => $canManagePages,
        ]));

        if ($container === null && Yii::$app->user->can(ManageModules::class)) {
            $this->addEntry(new MenuLink([
                'label' => Yii::t('CustomPagesModule.base', 'Templates'),
                'url' => Url::toTemplateAdmin(),
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
