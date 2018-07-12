<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

namespace humhub\modules\custom_pages\models\forms;

use humhub\modules\custom_pages\components\Container;
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
     * @var integer
     */
    public $type;
    
    /**
     * Defines the page type to be created (Page,Snippet,ContainerPage,...).
     * @var string
     */
    public $class;
    
    /**
     * Singleton page instance used for retrieving some page data as the page label.
     * @var mixed
     */
    private $_instance;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['type', 'validateType'],
            ['type', 'required'],
        ];
    }

    public function validateType($attribute, $params)
    {
        if(!$this->isAllowedType($this->type)) {
            $this->addError('type', Yii::t('CustomPagesModule.base', 'Invalid type selection'));
        }
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
     * @param integer $type
     * @return boolean
     */
    public function isAllowedType($type)
    {
        if($type === Container::TYPE_PHP) {
            $settings = new SettingsForm();
            if(!$settings->phpPagesActive) {
                return false;
            }
        }

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
     * @return integer
     */
    public function getPageInstance()
    {
        if($this->_instance == null) {
            $this->_instance = Yii::createObject($this->class);
        }
        return $this->_instance;
    }

    public function hasPHPFiles()
    {
        $settings = new SettingsForm();
        return  $settings->phpPagesActive && $this->getPageInstance()->hasAllowedPhpViews();
    }

}
