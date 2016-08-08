<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\widgets;

use Yii;

/**
 * User Administration Menu
 *
 * @author Basti
 */
class TemplateElementAdminRow extends \humhub\components\Widget
{
    public $model;
    public $form;
    public $saved;

    public function run()
    {
        if($this->form != null) {
            $this->model = $this->form->element;
        }
        
        if($this->saved) {
            Yii::$app->getSession()->setFlash('data-saved', Yii::t('CustomPagesModule.base', 'Saved'));
        }
        
        return $this->render('templateElementAdminRow', [
            'model' => $this->model,
            'saved' => $this->saved
        ]);
    }

}
