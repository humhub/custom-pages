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

    public $title;
    public $editDefault = false;

    public function rules()
    {
        return [
            ['title', 'string'],
        ];
    }

    public function scenarios()
    {
        return [
            'edit' => ['title'],
        ];
    }

    public function setItem($itemId)
    {
        $this->item = ContainerItem::findOne(['id' => $itemId]);
        $this->title = $this->item->title;
        $this->owner = $this->item->templateInstance;
        $this->setTemplate($this->owner->template_id);
    }

    public function save()
    {
        if (parent::save()) {
            $this->item->title = $this->title;
            return $this->item->save();
        }

        return true;
    }

}
