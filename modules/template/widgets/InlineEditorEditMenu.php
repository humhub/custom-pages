<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\widgets;

/**
 * User Administration Menu
 *
 * @author Basti
 */
class InlineEditorEditMenu extends \humhub\components\Widget
{
    public $canEdit;
    public $editMode;
    public $pageId;
    public $templateInstance;

    public function run()
    {
        return $this->render('inlineEditorEditMenu', [
            'canEdit' => $this->canEdit,
            'editMode' => $this->editMode,
            'pageId' => $this->pageId,
            'templateInstance' => $this->templateInstance
        ]);
    }

}
