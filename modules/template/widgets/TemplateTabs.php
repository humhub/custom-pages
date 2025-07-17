<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\widgets;

use humhub\helpers\ControllerHelper;
use humhub\modules\custom_pages\modules\template\models\Template;
use humhub\modules\ui\menu\MenuLink;
use humhub\modules\ui\menu\widgets\SubTabMenu;
use Yii;

class TemplateTabs extends SubTabMenu
{
    /**
     * @var Template
     */
    public $model;

    public function beforeRun()
    {
        return parent::beforeRun() && !$this->model->isNewRecord;
    }

    public function init()
    {
        $this->addEntry(new MenuLink([
            'label' => Yii::t('CustomPagesModule.base', 'General'),
            'url' => ['edit', 'id' => $this->model->id],
            'sortOrder' => 100,
            'isActive' => ControllerHelper::isActivePath('template', 'admin', 'edit'),
        ]));

        $this->addEntry(new MenuLink([
            'label' => Yii::t('CustomPagesModule.base', 'Source'),
            'url' => ['edit-source', 'id' => $this->model->id],
            'sortOrder' => 200,
            'isActive' => ControllerHelper::isActivePath('template', 'admin', 'edit-source'),
        ]));

        $this->addEntry(new MenuLink([
            'label' => Yii::t('CustomPagesModule.base', 'Resources'),
            'url' => ['edit-resources', 'id' => $this->model->id],
            'sortOrder' => 300,
            'isActive' => ControllerHelper::isActivePath('template', 'admin', 'edit-resources'),
        ]));

        $this->addEntry(new MenuLink([
            'label' => Yii::t('CustomPagesModule.base', 'Usage'),
            'url' => ['edit-usage', 'id' => $this->model->id],
            'sortOrder' => 400,
            'isActive' => ControllerHelper::isActivePath('template', 'admin', 'edit-usage'),
        ]));

        parent::init();
    }
}
