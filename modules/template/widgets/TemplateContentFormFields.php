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
 * @author buddh4
 */
class TemplateContentFormFields extends \humhub\components\Widget
{
    public $view;
    public $type;
    public $form;
    public $model;
    public $fileList;

    public function run()
    {
        if($this->view == null) {
            $this->view = $this->type.'ContentFormFields';
        }
        
        return $this->render($this->view, [
            'form' => $this->form,
            'model' => $this->model,
            'fileList' => $this->fileList,
            'isAdminEdit' =>  $this->model->scenario === 'edit-admin' || $this->model->scenario === 'create'
        ]);
    }

}
