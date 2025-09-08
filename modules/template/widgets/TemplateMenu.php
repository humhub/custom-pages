<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\widgets;

use humhub\modules\custom_pages\modules\template\models\Template;
use humhub\modules\ui\menu\MenuLink;
use humhub\modules\ui\menu\widgets\Menu;
use Yii;
use yii\helpers\Url;

class TemplateMenu extends Menu
{
    /**
     * @inheritdoc
     */
    public $template = 'templateMenu';

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
        if ($this->model->canEdit()) {
            $this->addEntry(new MenuLink([
                'label' => Yii::t('CustomPagesModule.template', 'Edit all elements'),
                'url' => ['edit', 'id' => $this->model->id],
                'icon' => 'pencil',
                'htmlOptions' => [
                    'data-action-click' => 'ui.modal.load',
                    'data-action-data-type' => 'json',
                    'data-action-url' => Url::to(['/custom_pages/template/admin/edit-multiple', 'id' => $this->model->id]),
                ],
                'sortOrder' => 100,
            ]));
        }

        $this->addEntry(new MenuLink([
            'label' => Yii::t('CustomPagesModule.template', 'Copy'),
            'url' => ['copy', 'id' => $this->model->id],
            'icon' => 'copy',
            'sortOrder' => 200,
        ]));

        $this->addEntry(new MenuLink([
            'label' => Yii::t('CustomPagesModule.template', 'Export'),
            'url' => ['export-source', 'id' => $this->model->id],
            'icon' => 'upload',
            'sortOrder' => 300,
        ]));

        if ($this->model->canEdit()) {
            $this->addEntry(new MenuLink([
                'label' => Yii::t('CustomPagesModule.template', 'Delete'),
                'url' => ['delete-template', 'id' => $this->model->id],
                'icon' => 'trash',
                'htmlOptions' => [
                    'data-action-confirm' => '',
                ],
                'sortOrder' => 400,
            ]));
        }

        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function getAttributes()
    {
        return [
            'class' => 'dropdown-navigation float-end',
            'style' => 'margin-left:16px',
        ];
    }
}
