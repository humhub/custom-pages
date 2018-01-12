<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\models\forms;

/**
 * Form model used to add new TemplateElement instances to a Template.
 *
 * @author buddha
 */
class ContentFormItem extends \yii\base\Model
{

    public $ownerContent;
    public $editDefault = true;
    public $content;
    public $element;
    public $isLoaded = false;
    public $key;

    public function init()
    {
        $this->content = $this->ownerContent->getInstance(true);

        if ($this->ownerContent->isNewRecord) {
            $this->key = $this->ownerContent->element_name;
        } else {
            $this->key = $this->ownerContent->id;
        }

        $this->content->setFormName('Content[' . $this->key . ']');
        $this->content->scenario = $this->scenario;
    }

    public function load($data, $formName = NULL)
    {
        if (!isset($data['Content']) || !isset($data['Content'][$this->key])) {
            return false;
        }
        
        $values = $data['Content'][$this->key];
        $this->content->load(['content' => $values], 'content');
        $this->isLoaded = !$this->isEmptySubmit($values);
        return true;
    }

    private function isEmptySubmit($values)
    {
        foreach ($values as $key => $value) {
            if (!empty($value)) {
                return false;
            }
        }
        return true;
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

        if ($this->ownerContent->isDefault() && !$this->editDefault) {
            $fileList = $this->content->fileList;
            $this->content = $this->content->copy();
            $this->content->fileList = $fileList;
        }
        
        if ($this->content->isNewRecord) {
            $this->element->saveInstance($owner, $this->content);
        } else {
            $this->content->save(false);
        }
        
        return true;
    }

}
