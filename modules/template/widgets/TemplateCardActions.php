<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\widgets;

use humhub\widgets\menu\CardHeaderActions;
use humhub\widgets\menu\MenuLink;
use Yii;

/**
 * The header actions of the template table ({@see TemplateTable}): "Import" and "Create".
 */
class TemplateCardActions extends CardHeaderActions
{
    /**
     * @inheritdoc
     */
    public $id = 'custom-pages-template-card-actions';

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->addEntry(new MenuLink([
            'id' => 'import-template',
            'label' => Yii::t('CustomPagesModule.base', 'Import'),
            'url' => ['/custom_pages/template/admin/import-source'],
            'icon' => 'download',
            'htmlOptions' => ['data-action-click' => 'ui.modal.load'],
            'sortOrder' => 100,
        ]));

        $this->addEntry(new MenuLink([
            'id' => 'create-template',
            'label' => Yii::t('CustomPagesModule.base', 'Create'),
            'url' => ['/custom_pages/template/admin/edit'],
            'icon' => 'plus',
            'isPrimary' => true,
            'sortOrder' => 200,
        ]));

        parent::init();
    }
}
