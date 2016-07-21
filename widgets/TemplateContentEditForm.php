<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\widgets;

use Yii;
use yii\helpers\Url;

/**
 * User Administration Menu
 *
 * @author Basti
 */
class TemplateContentEditForm extends \humhub\components\Widget
{
    public $view;
    public $form;
    public $model;

    public function run()
    {
        return $this->render($this->view, [
            'form' => $this->form,
            'model' => $this->model
        ]);
    }

}
