<?php

namespace humhub\modules\custom_pages\modules\template\models;

use Yii;

/**
 * This is the model class for table "custom_pages_template_content".
 * 
 * A OwnerContent instance is used to assign a TemplateElement to a specific
 * Content of a specific type.
 */
class AssetVariable implements \ArrayAccess
{
    
    private $module;
    
    public function get($name)
    {
        $path = '/'.$name;
        
        $module = $this->getModule();
        if($module->isPublished($path)) {
            return $this->getModule()->getPublishedUrl($path);
        }
        return '';
    }
    
    private function getModule()
    {
        if($this->module == null) {
            $this->module = Yii::$app->getModule('custom_pages');
        }
        return $this->module;
    }
    
    public function __toString()
    {
        return '';
    }

    public function offsetExists($offset)
    {
        return true;
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value)
    {
        return;
    }

    public function offsetUnset($offset)
    {
        return;
    }

}
