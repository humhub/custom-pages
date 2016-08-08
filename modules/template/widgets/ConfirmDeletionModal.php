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
class ConfirmDeletionModal extends \humhub\components\Widget
{

    public $title;
    public $message;
    public $successEvent;
    public $errorEvent;
    
    public function run()
    {
        if($this->successEvent == null) {
            $this->successEvent = 'templateElementDeleteSuccess';
        }
        
         if($this->errorEvent == null) {
            $this->errorEvent = 'templateElementDeleteError';
        }
        
        return $this->render('confirmDeletionModal', [
            'title' => $this->title,
            'message' => $this->message,
            'successEvent' => $this->successEvent,
            'errorEvent' => $this->errorEvent
        ]);
    }

}
