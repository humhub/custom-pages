<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\widgets;

use Yii;
use humhub\modules\custom_pages\modules\template\models\PagePermission;

/**
 * User Administration Menu
 *
 * @author buddha
 */
class PageConfigurationButton extends \humhub\components\Widget
{
    /**
     * @var integer
     */
    public $pageId;

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
        if (!PagePermission::canEdit()) {
            return;
        }

        $pageId = isset($this->pageId) ? $this->pageId : Yii::$app->request->get('id');

        $contentContainer = isset(Yii::$app->controller->contentContainer) ? Yii::$app->controller->contentContainer : null;

        return $this->render('pageConfigurationButton', [
            'pageId' => $pageId,
            'contentContainer' => $contentContainer,
            'target' => $this->target,
            'btnClass' => $this->btnClass,
            'btnStyles' => $this->btnStyles,
        ]);
    }

}
