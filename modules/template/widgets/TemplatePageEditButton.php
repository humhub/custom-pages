<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\widgets;

use humhub\components\Widget;
use Yii;
use humhub\modules\custom_pages\models\CustomPage;
use humhub\modules\custom_pages\modules\template\models\TemplateInstance;

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
     * @var bool
     */
    public $canEdit;

    /**
     * @var string
     */
    public $mode;

    /**
     * @inheritdoc
     */
    public function run()
    {
        if (!$this->canEdit) {
            return '';
        }

        $space = Yii::$app->controller->contentContainer ?? null;

        $templateInstance = TemplateInstance::findOne(['page_id' => $this->page->id]);

        return $this->render('templatePageEditButton', [
            'canEdit' => $this->canEdit,
            'sguid' => $space ? $space->guid : null,
            'mode' => $this->mode,
            'pageId' => $this->page->id,
            'templateInstance' => $templateInstance,
        ]);
    }
}
