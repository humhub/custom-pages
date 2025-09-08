<?php

namespace humhub\modules\custom_pages\modules\template\models;

use Yii;

/**
 * This is the model class for table "custom_pages_template_content".
 *
 * An ElementContent instance is used to assign a TemplateElement to a specific
 * Content of a specific type.
 *
 * @warning Compatible only with PHP8.0+, Don't use for PHP versions <= 7.4!
 */
class AssetVariable implements \ArrayAccess
{
    private $module;

    public function get($name)
    {
        $path = '/' . $name;

        $module = $this->getModule();
        if ($module->isPublished($path)) {
            return $this->getModule()->getPublishedUrl($path);
        }
        return '';
    }

    private function getModule()
    {
        if ($this->module == null) {
            $this->module = Yii::$app->getModule('custom_pages');
        }
        return $this->module;
    }

    public function __toString()
    {
        return '';
    }

    public function offsetExists($offset): bool
    {
        return true;
    }

    public function offsetGet($offset): mixed
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value): void
    {
    }

    public function offsetUnset($offset): void
    {
    }

}
