<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\models\forms;

use humhub\modules\custom_pages\modules\template\elements\BaseElementContent;
use humhub\modules\custom_pages\modules\template\elements\ContainerItem;
use humhub\modules\custom_pages\modules\template\models\TemplateElement;
use humhub\modules\custom_pages\modules\template\models\TemplateInstance;
use yii\base\Model;

/**
 * Form model used to add new TemplateElement instances to a Template.
 *
 * @author buddha
 */
class ContentFormItem extends Model
{
    public ?BaseElementContent $elementContent = null;
    public bool $editDefault = true;
    public ?BaseElementContent $content = null;
    public ?TemplateElement $element = null;
    public bool $isLoaded = false;
    public $key;

    public function init()
    {
        $this->content = $this->elementContent->getInstance(true);
        $this->content->element_id = $this->element->id;

        if ($this->elementContent->isNewRecord) {
            $this->key = $this->elementContent->element->name;
        } else {
            $this->key = $this->elementContent->id;
        }

        $this->content->setFormName('Content[' . $this->key . ']');
        $this->content->scenario = $this->scenario;
    }

    public function load($data, $formName = null)
    {
        if (!isset($data['Content']) || !isset($data['Content'][$this->key])) {
            return false;
        }

        $values = $data['Content'][$this->key];
        $this->content->load(['content' => $values], 'content');
        $this->isLoaded = !$this->isEmptySubmit($values);
        return true;
    }

    private function isEmptySubmit($values): bool
    {
        return empty($values);
    }

    public function validate($attributeNames = null, $clearErrors = true)
    {
        if ($this->content->isNewRecord && !$this->isLoaded && !$this->content->hasValues()) {
            return true;
        }

        return $this->content->validate();
    }

    public function save($owner)
    {
        if (!$this->isLoaded) {
            return true;
        }

        if ($this->elementContent->isDefault() && !$this->editDefault) {
            $fileList = $this->content->fileList;
            $this->content = $this->content->copy();
            $this->content->fileList = $fileList;
        }

        if ($this->content->isNewRecord) {
            return (bool) $this->element->saveInstance($owner, $this->content);
        }

        return $this->content->save(false);
    }

}
