<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\widgets;

use humhub\components\Widget;
use Yii;
use humhub\modules\custom_pages\models\Page;
use humhub\modules\custom_pages\modules\template\models\TemplateInstance;

/**
 * User Administration Menu
 *
 * @author Basti
 */
class TemplatePageEditButton extends Widget
{
    /**
     * @var Page page instance
     */
    public $page;

    /**
     * @var bool
     */
    public $canEdit;

    /**
     * @var bool
     */
    public $editMode;

    /**
     * @inheritdoc
     */
    public function run()
    {
        if (!$this->canEdit) {
            return '';
        }

        $space = Yii::$app->controller->contentContainer ?? null;

        $templateInstance = TemplateInstance::findOne(['object_model' => Page::class, 'object_id' => $this->page->id]);

        return $this->render('templatePageEditButton', [
            'canEdit' => $this->canEdit,
            'sguid' => $space ? $space->guid : null,
            'editMode' => $this->editMode,
            'pageId' => $this->page->id,
            'templateInstance' => $templateInstance,
        ]);
    }
}
