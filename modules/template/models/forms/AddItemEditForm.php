<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\models\forms;

use humhub\modules\custom_pages\modules\template\elements\ContainerElement;
use humhub\modules\custom_pages\modules\template\elements\ContainerItem;

/**
 * Form model used for adding container items.
 *
 * @author buddha
 */
class AddItemEditForm extends EditItemForm
{
    public ?ContainerElement $elementContent = null;

    /**
     * @var ContainerItem $owner
     */
    public $owner;

    public function setItemTemplate($itemTemplate)
    {
        $this->template = $itemTemplate;
        $this->owner = $this->elementContent->createEmptyItem($this->template->id);
        $this->prepareContentInstances();
    }

    public function save(): bool
    {
        $this->owner->save();
        return parent::save();
    }
}
