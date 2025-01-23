<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\models\forms;

use humhub\modules\custom_pages\modules\template\elements\BaseTemplateElementContent;
use humhub\modules\custom_pages\modules\template\elements\ContainerElement;

/**
 * Form model used for adding container items.
 *
 * @author buddha
 */
class AddItemEditForm extends EditItemForm
{
    public ?ContainerElement $elementContent = null;

    public function setItemTemplate($itemTemplate)
    {
        $this->template = $itemTemplate;
        $this->owner = $this->elementContent->createEmptyItem($this->template->id);
        $this->prepareContentInstances();
    }

    public function save()
    {
        $this->owner->title = $this->title;
        $this->owner->save();
        return parent::save(false);
    }

}
