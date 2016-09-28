<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\widgets;

/**
 * Content Type Row for adding a new Page/Snippet of a certain type
 *
 * @author buddha
 */
class AddContentTypeRow extends \yii\base\Widget
{

    public $type;
    public $label;
    public $description;
    public $disabled = false;
    
    public $hide = false;
    
    public function run()
    {
        if(!$this->hide) {
            return $this->render('addContentTypeRow', [
                'type' => $this->type,
                'label' => $this->label,
                'description' => $this->description,
                'disabled' => $this->disabled
            ]);
        }
    }
}
