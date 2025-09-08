<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\widgets;

use humhub\components\Widget;
use humhub\modules\custom_pages\modules\template\elements\BaseElementContent;
use humhub\widgets\form\ActiveForm;

/**
 * Widget renders a form with fields of the Template Element Content
 *
 * @author buddh4
 */
class TemplateContentFormFields extends Widget
{
    public ?ActiveForm $form = null;
    public ?BaseElementContent $model = null;

    public function run()
    {
        return $this->model->renderEditForm($this->form);
    }
}
