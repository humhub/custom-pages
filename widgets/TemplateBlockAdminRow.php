<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\widgets;

/**
 * User Administration Menu
 *
 * @author Basti
 */
class TemplateBlockAdminRow extends \humhub\components\Widget
{
    public $model;
    public $form;

    public function run()
    {
        if($this->form != null) {
            $this->model = $this->form->templateBlock;
        }
        return $this->render('templateBlockAdminRow', [
            'model' => $this->model
        ]);
    }

}
