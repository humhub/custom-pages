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
class EditElementModal extends \humhub\components\Widget
{
    public $model;
    public $title;
    public $contentOnly = false;
    public $resetUrl;
    public $isAdminEdit = false;

    public function run()
    {
        return $this->render('editElementModal', [
            'model' => $this->model,
            'title' => $this->title,
            'contentOnly' => $this->contentOnly,
            'resetUrl' => $this->resetUrl,
            'isAdminEdit' => $this->isAdminEdit
        ]);
    }

}
