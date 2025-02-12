<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\models\forms;

use humhub\modules\custom_pages\modules\template\elements\BaseElementContent;

/**
 * Description of UserGroupForm
 *
 * @author buddha
 */
class TemplateElementForm extends \yii\base\Model
{
    public const SCENARIO_CREATE = 'create';
    public const SCENARIO_EDIT = 'edit';
    public const SCENARIO_EDIT_ADMIN = 'edit-admin';

    /**
     * The TemplateElement instance.
     *
     * @var \humhub\modules\custom_pages\modules\template\models\TemplateElement
     */
    public $element;

    /**
     * Default content instance.
     *
     * @var BaseElementContent
     */
    public $content;

    /**
     * @inheritdoc
     * @var string
     */
    public $scenario = 'edit';

    public function scenarios()
    {
        return [
            self::SCENARIO_CREATE => [],
            self::SCENARIO_EDIT_ADMIN => [],
            self::SCENARIO_EDIT => [],
        ];
    }

    public function setScenario($value)
    {
        parent::setScenario($value);
        if ($this->element != null) {
            $this->element->scenario = $value;
        }
        if ($this->content != null) {
            $this->content->scenario = $value;
        }
    }

    public function load($data, $formName = null)
    {
        parent::load($data);

        $result = false;
        if ($this->content != null) {
            $result = $this->content->load($data, $formName);
        }

        $elementLoaded = $this->element->load($data, $formName);

        // Note, only the template element loading is mandatory.
        return $result || $elementLoaded;
    }

    public function validate($attributeNames = null, $clearErrors = true)
    {
        return parent::validate($attributeNames, $clearErrors) &&
            $this->element->validate($attributeNames, $clearErrors) &&
            $this->content->validate($attributeNames, $clearErrors);
    }

    public function getLabel()
    {
        return BaseElementContent::createByType($this->element->content_type)->getLabel();
    }
}
