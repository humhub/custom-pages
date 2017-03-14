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
use humhub\modules\custom_pages\modules\template\models\TemplateInstance;

/**
 * User Administration Menu
 *
 * @author Basti
 */
class TemplatePageEditButton extends \humhub\components\Widget
{

    /**
     * @var \humhub\modules\custom_pages\models\CustomContentContainer page instance
     */
    public $page;

    /**
     * @var boolean
     */
    public $canEdit;

    /**
     * @var boolean
     */
    public $editMode;

    /**
     * @inheritdoc
     */
    public function run()
    {
        if (!$this->canEdit) {
            return;
        }

        $space = (isset(Yii::$app->controller->contentContainer)) ? Yii::$app->controller->contentContainer : null;
        $sguid = ($space) ? $space->guid : null;
        $ownerModel = ($space) ? ContainerPage::className() : Page::className();

        $templateInstance = TemplateInstance::findOne(['object_model' => $ownerModel, 'object_id' => $this->page->id]);

        return $this->render('templatePageEditButton', [
                    'canEdit' => $this->canEdit,
                    'sguid' => $sguid,
                    'editMode' => $this->editMode,
                    'pageId' => $this->page->id,
                    'templateInstance' => $templateInstance
        ]);
    }
}
