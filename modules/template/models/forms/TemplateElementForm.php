<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\models\forms;

use Yii;

/**
 * Description of UserGroupForm
 *
 * @author buddha
 */
class TemplateElementForm extends \yii\base\Model
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_EDIT = 'edit';
    const SCENARIO_EDIT_ADMIN = 'edit-admin';
    
    /**
     * The TemplateElement instance.
     * 
     * @var \humhub\modules\custom_pages\modules\template\models\TemplateElement 
     */
    public $element;

    /**
     * Default content instance.
     * 
     * @var \humhub\modules\custom_pages\modules\template\models\TemplateContentActiveRecord 
     */
    public $content;
    
    /**
     * OwnerContent use_default flag
     * @var boolean 
     */
    public $use_default;

    /**
     * @inheritdoc
     * @var string
     */
    public $scenario = 'edit';

    public function rules()
    {
        return [
            ['use_default', 'safe']
        ];
    }
    
    public function scenarios()
    {
        return [
            self::SCENARIO_CREATE => ['use_default'],
            self::SCENARIO_EDIT_ADMIN => ['use_default'],
            self::SCENARIO_EDIT => ['use_default'],
        ];
    }
    
    public function setScenario($value)
    {
        parent::setScenario($value);
        if($this->element != null) {
            $this->element->scenario = $value;
        }
        if($this->content != null) {
            $this->content->scenario = $value;
        }
    }
    
    public function load($data, $formName = null)
    {
        parent::load($data);

        $result = false;
        if ($this->content != null) {
            $result = $result || $this->content->load($data, $formName);
        }

        $elementLoaded = $this->element->load($data, $formName);

        // Note, only the template element loading is mandatory.
        return $result || $elementLoaded;
    }

    public function validate($attributeNames = NULL, $clearErrors = true)
    {
        return parent::validate() && $this->element->validate();
    }

    public function getLabel()
    {
        return Yii::createObject($this->element->content_type)->getLabel();
    }
}
