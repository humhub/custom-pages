<?php

namespace humhub\modules\custom_pages\modules\template\models;

use yii\base\Model;

class OwnerContentVariable extends Model
{

    public $options = [];

    /**
     * @var OwnerContent
     */
    public $ownerContent;

    public function getLabel()
    {
        return $this->ownerContent->getLabel();
    }

    public function isEditMode()
    {
        return (isset($this->options['editMode'])) ? $this->options['editMode'] : false;
    }

    public function getEmptyContent()
    {
        return $this->ownerContent->renderEmpty();
    }

    public function getEmpty()
    {
        return $this->ownerContent->isEmpty();
    }

    public function getContent()
    {
        return $this->ownerContent->instance;
    }

    public function render($editMode = false)
    {
        if($editMode) {
            $this->options['editMode'] = true;
        }

        if(isset($this->options['editMode']) && $this->options['editMode']) {
            $options = array_merge([
                'empty' => $this->ownerContent->isEmpty(),
                'owner_content_id' => $this->ownerContent->id,
                'element_name' => $this->ownerContent->element_name,
                'owner_model' => $this->ownerContent->owner_model,
                'owner_id' => $this->ownerContent->owner_id,
                'default' => $this->ownerContent->isDefault(),
            ], $this->options);

            // We only need the template_id for container content elements
            if($this->ownerContent->content_type == ContainerContent::class) {
                $options['template_id'] = $this->ownerContent->owner->getTemplateId();
            }
        } else {
            $options = $this->options;
        }

        try {
            if(!$this->ownerContent->isEmpty()) {
                return $this->ownerContent->render($options);
            } else if($this->isEditMode()) {
                return $this->ownerContent->renderEmpty($options);
            }
        } catch(\Exception $e) {
            return strval($e);
        }

        return '';
    }

    public function __toString()
    {
        // Note that the editMode can be set to $this->options in this case
        return $this->render();
    }


}
