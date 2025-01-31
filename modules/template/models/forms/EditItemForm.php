<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\models\forms;

use humhub\modules\custom_pages\modules\template\elements\ContainerItem;

/**
 * Description of UserGroupForm
 *
 * @author buddha
 */
class EditItemForm extends EditMultipleElementsForm
{
    public ?ContainerItem $item = null;

    public $editDefault = false;

    public function scenarios()
    {
        return [
            'edit' => [],
        ];
    }

    public function setItem($itemId)
    {
        $this->item = ContainerItem::findOne(['id' => $itemId]);
        $this->owner = $this->item->templateInstance;
        $this->setTemplate($this->owner->template_id);
    }

    public function save(): bool
    {
        if (parent::save()) {
            if ($this->item instanceof ContainerItem) {
                return $this->item->save();
            }
            return true;
        }
        return false;
    }

}
