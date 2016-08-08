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
 * @author buddha
 */
class EditContainerItemModal extends \humhub\components\Widget
{
    public $model;
    public $title;
    public $action;

    public function run()
    {
        return $this->render('editItemModal', [
            'model' => $this->model,
            'title' => $this->title,
            'action' => $this->action
        ]);
    }

}
