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
class TemplateElementAdminRow extends \humhub\components\Widget
{
    /**
     * @var \humhub\modules\custom_pages\modules\template\models\TemplateElement
     */
    public $model;

    /**
     * @var \humhub\modules\custom_pages\modules\template\models\forms\TemplateElementForm
     */
    public $form;

    public function run()
    {
        if ($this->form != null) {
            $this->model = $this->form->element;
        }

        return $this->render('templateElementAdminRow', [
            'model' => $this->model,
        ]);
    }

}
