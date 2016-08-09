<?php

namespace humhub\modules\custom_pages\modules\template\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "custom_pages_page".
 *
 * The followings are the available columns in table 'custom_pages_page':
 */
abstract class ContentDefinition extends ActiveRecord
{

    private $formName;

    public function setFormName($formName)
    {
        $this->formName = $formName;
    }

    public function hasValues()
    {
        $result = false;
        foreach ($this->attributes() as $key) {
            if ($this->getAttribute($key) != null && $key != 'id' && $key != 'is_default') {
                $result = true;
                break;
            }
        }
        return $result;
    }

    public function formName()
    {
        return ($this->formName != null) ? $this->formName : parent::formName();
    }

    public function load($data, $formName = null)
    {
        parent::load($data, $formName);
    }

}
