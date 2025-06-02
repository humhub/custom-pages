<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\elements;

use humhub\modules\custom_pages\modules\template\components\ActiveRecordDynamicAttributes;

/**
 * Class for template element definition
 */
abstract class BaseElementDefinition extends ActiveRecordDynamicAttributes
{
    private $formName;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'custom_pages_template_element';
    }

    public function setFormName($formName): void
    {
        $this->formName = $formName;
    }

    public function hasValues(): bool
    {
        foreach ($this->attributes() as $key) {
            if ($this->getAttribute($key) !== null && $key !== 'id') {
                return true;
            }
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function formName()
    {
        return $this->formName ?? parent::formName();
    }
}
