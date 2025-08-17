<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\widgets;

use humhub\modules\custom_pages\models\CustomPage;
use humhub\modules\custom_pages\modules\template\helpers\PagePermissionHelper;
use Yii;

/**
 * User Administration Menu
 *
 * @author buddha
 */
class PageConfigurationButton extends \humhub\components\Widget
{
    public ?int $pageId = null;

    /**
     * @var string
     */
    public $target = '_blank';

    /**
     * @var string
     */
    public $btnClass = 'btn btn-primary btn-xs';

    /**
     * @var string
     */
    public $btnStyles = 'margin-bottom:5px';

    public function run()
    {
        $pageId = $this->pageId ?? (int)Yii::$app->request->get('id');
        $page = CustomPage::findOne($pageId);
        if (!$page) {
            return '';
        }

        if (!PagePermissionHelper::canEdit($page)) {
            return '';
        }

        $contentContainer = Yii::$app->controller->contentContainer ?? null;

        return $this->render('pageConfigurationButton', [
            'pageId' => $pageId,
            'contentContainer' => $contentContainer,
            'target' => $this->target,
            'btnClass' => $this->btnClass,
            'btnStyles' => $this->btnStyles,
        ]);
    }

}
