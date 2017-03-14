<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\widgets;

use yii\helpers\Url;

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
        $this->action = (!$this->action) ? Url::to() : $this->action;
        
        return $this->render('editItemModal', [
            'model' => $this->model,
            'title' => $this->title,
            'action' => $this->action
        ]);
    }

}
