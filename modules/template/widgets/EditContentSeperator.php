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
class EditContentSeperator extends \humhub\components\Widget
{

    public $isAdminEdit;
    
    public function run()
    {
        return $this->render('editContentSeperator', [
            'isAdminEdit' => $this->isAdminEdit
        ]);
    }

}
