<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\models;

use Yii;
use yii\base\Model;

/**
 * AddPageForm selects a page type
 *
 * @author luke
 */
class AddPageForm extends Model
{

    public $type;
    public $class;
    public $isAdmin = false;
    
    private $_instance;

    public function rules()
    {
        return array(
            ['type', 'in', 'range' => array_values($this->getPageInstance()->getContentTypes())],
        );
    }
    
    public function load($data, $formName = null)
    {
        if(isset($data['type'])) {
            $this->type = $data['type'];
            return true;
        } else {
            return parent::load($data, $formName = null);
        }
    }
    
    public function getPageLabel()
    {
        return $this->getPageInstance()->getLabel();
    }
    
    public function isAllowedType($type)
    {
        return in_array($type ,$this->getPageInstance()->getContentTypes());
    }
    
    public function showTemplateType()
    {
        return count($this->getPageInstance()->getAllowedTemplateSelection()) > 0;
    }
    
    public function getPageInstance()
    {
        if($this->_instance == null) {
            $this->_instance = Yii::createObject($this->class);
        }
        return $this->_instance;
    }

}
