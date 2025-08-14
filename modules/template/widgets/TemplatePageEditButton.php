<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\widgets;

use humhub\components\Widget;
use humhub\modules\content\helpers\ContentContainerHelper;
use humhub\modules\custom_pages\models\CustomPage;
use humhub\modules\custom_pages\modules\template\helpers\PagePermissionHelper;

/**
 * User Administration Menu
 *
 * @author Basti
 */
class TemplatePageEditButton extends Widget
{
    /**
     * @var CustomPage page instance
     */
    public $page;

    /**
     * @inheritdoc
     */
    public function beforeRun()
    {
        return parent::beforeRun() && PagePermissionHelper::canEdit();
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        return $this->render('templatePageEditButton', [
            'page' => $this->page,
            'container' => ContentContainerHelper::getCurrent(),
        ]);
    }
}
