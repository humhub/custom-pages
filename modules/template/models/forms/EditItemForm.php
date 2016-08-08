<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\models\forms;

use humhub\modules\custom_pages\modules\template\models\ContainerContentItem;

/**
 * Description of UserGroupForm
 *
 * @author buddha
 */
class EditItemForm extends EditMultipleElementsForm
{

    public $title;
    public $editDefault = false;
    
    public function rules()
    {
        return [
            ['title', 'string']
        ];
    }
    
    public function scenarios()
    {
        return [
            'edit' => ['title']
        ];
    }
    
    public function setItem($itemId)
    {
        $this->owner = ContainerContentItem::findOne(['id' => $itemId]);
        $this->title = $this->owner->title;
        $this->setTemplate($this->owner->template_id);
    }
    
    public function save()
    {
        if(parent::save(false)) {
            $this->owner->title = $this->title;
            $this->owner->save();
        }

        return true;
    }
   
}
