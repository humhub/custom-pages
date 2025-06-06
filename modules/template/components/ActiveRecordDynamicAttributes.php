<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\components;

use humhub\components\ActiveRecord;
use yii\validators\Validator;

/**
 * Abstract ActiveRecord which allows you to use not only the regular AR attributes but also other dynamic attributes.
 * These are stored in a JSON field.
 *
 * @property int $id
 * @property string|array $dyn_attributes
 *
 * Dynamic attributes:
 * (List here all virtual/dynamic for the Record,
 *  they all are stored in the property $dyn_attributes as json encoded array)
 *
 * @todo Avoid mark `dyn_attributes` model attribute as Safe attribute
 */
abstract class ActiveRecordDynamicAttributes extends ActiveRecord
{
    /**
     * Get all possible dynamic attributes for this element content
     *
     * @return array Key - element index name, Value - default value
     */
    abstract protected function getDynamicAttributes(): array;

    /**
     * @inheritdoc
     */
    public function __get($name)
    {
        if ($this->hasDynamicAttribute($name)) {
            return $this->dyn_attributes[$name] ?? $this->getDynamicAttributeDefaultValue($name);
        }

        $value = parent::__get($name);

        if ($name === 'dyn_attributes' && !is_array($value)) {
            $value = empty($value) ? [] : json_decode($value, true);
            $this->setAttribute($name, $value);
        }

        return $value;
    }

    /**
     * @inheritdoc
     */
    public function __set($name, $value)
    {
        if ($this->hasDynamicAttribute($name)) {
            $attrs = $this->dyn_attributes;
            $attrs[$name] = $value;
            $this->setAttribute('dyn_attributes', $attrs);
        } else {
            parent::__set($name, $value);
        }
    }

    /**
     * @inheritdoc
     * @noinspection PhpMissingReturnTypeInspection
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->dyn_attributes = is_array($this->dyn_attributes) && ! empty($this->dyn_attributes)
                ? json_encode($this->dyn_attributes)
                : null;
            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function createValidators()
    {
        $validators = parent::createValidators();
        $validators->append(Validator::createValidator('safe', $this, 'dyn_attributes'));

        return $validators;
    }

    public function hasDynamicAttribute(string $name): bool
    {
        return array_key_exists($name, $this->getDynamicAttributes());
    }

    private function getDynamicAttributeDefaultValue(string $name): mixed
    {
        return $this->getDynamicAttributes()[$name] ?? null;
    }

}
