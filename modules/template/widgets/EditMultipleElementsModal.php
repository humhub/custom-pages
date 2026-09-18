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
class EditMultipleElementsModal extends \humhub\components\Widget
{
    public $model;
    public $title;

    /**
     * @var string|null URL of the dialog to return to, e.g. the parent instance of a container item
     */
    public $backUrl = null;

    public function run()
    {
        return $this->render('editMultipleElements', [
            'model' => $this->model,
            'title' => $this->title,
            'backUrl' => $this->backUrl,
        ]);
    }

}
