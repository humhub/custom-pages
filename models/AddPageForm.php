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

    /**
     * Defines the page content type to be created (Markdown,Template,...)
     * @var type 
     */
    public $type;
    
    /**
     * Defines the page type to be created (Page,Snippet,ContainerPage,...).
     * @var type 
     */
    public $class;
    
    /**
     * Singleton page instance used for retrieving some page data as the page label.
     * @var type 
     */
    private $_instance;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array(
            ['type', 'in', 'range' => array_values($this->getPageInstance()->getContentTypes())],
            ['type', 'required'],
        );
    }
    
    /**
     * Helper function used by views.
     * 
     * @return string
     */
    public function getPageLabel()
    {
        return $this->getPageInstance()->getLabel();
    }
    
    /**
     * Tests if the given type is allowed for the given page class.
     * 
     * @param type $type
     * @return boolean
     */
    public function isAllowedType($type)
    {
        return in_array($type ,$this->getPageInstance()->getContentTypes());
    }
    
    /**
     * Checks if there are allowed templates available for the given page class.
     * 
     * @return boolean
     */
    public function showTemplateType()
    {
        return count($this->getPageInstance()->getAllowedTemplateSelection()) > 0;
    }
    
    /**
     * Returns the singleton page instance.
     * 
     * @return type
     */
    public function getPageInstance()
    {
        if($this->_instance == null) {
            $this->_instance = Yii::createObject($this->class);
        }
        return $this->_instance;
    }

}
