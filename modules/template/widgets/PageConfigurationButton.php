<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\widgets;

use Yii;
use humhub\modules\custom_pages\models\ContainerPage;
use humhub\modules\custom_pages\models\Page;
use humhub\modules\custom_pages\modules\template\models\PagePermission;
use humhub\modules\custom_pages\modules\template\models\TemplateInstance;

/**
 * User Administration Menu
 *
 * @author buddha
 */
class PageConfigurationButton extends \humhub\components\Widget
{
    public $canEdit;
    public $editMode;
    public $pageId;
    public $templateInstance;

    public function run()
    {
        $editMode = Yii::$app->request->get('editMode');
        $pageId = Yii::$app->request->get('id');

        $space = (isset(Yii::$app->controller->contentContainer)) ? Yii::$app->controller->contentContainer : null;

        $sguid = ($space != null) ? $space->guid : null;
        $canEdit = PagePermission::canEdit();

        $ownerModel = ($space != null) ? ContainerPage::class : Page::class;


        $templateInstance = TemplateInstance::findOne(['object_model' => $ownerModel ,'object_id' => $pageId]);

        return $this->render('pageConfigurationButton', [
            'canEdit' => $canEdit,
            'sguid' => $sguid,
            'editMode' => $editMode,
            'pageId' => $pageId,
            'templateInstance' => $templateInstance
        ]);
    }

}
