<?php

namespace humhub\modules\custom_pages\modules\template\components;

use humhub\components\ActiveRecord;
use yii\validators\Validator;

/**
 * Abstract ActiveRecord which allows you to use not only the regular AR attributes but also other dynamic attributes.
 * These are stored in a JSON field.
 *
 * @todo Avoid mark `dynAttribute` model attribute as Safe attribute
 */
abstract class ActiveRecordDynamicAttributes extends ActiveRecord
{
    /**
     * Get all possible fields for this element content
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
            return $this->dynAttributes[$name] ?? $this->getDynamicAttributeDefaultValue($name);
        }

        $value = parent::__get($name);

        if ($name === 'dynAttributes' && !is_array($value)) {
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
            $fields = $this->dynAttributes;
            $fields[$name] = $value;
            $this->setAttribute('dynAttributes', $fields);
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
            $this->dynAttributes = is_array($this->dynAttributes) ? json_encode($this->dynAttributes) : null;
            return true;
        }

        return parent::beforeSave($insert);
    }

    /**
     * @inheritdoc
     */
    public function createValidators()
    {
        $validators = parent::createValidators();
        $validators->append(Validator::createValidator('safe', $this, 'dynAttributes'));

        return $validators;
    }

    private function hasDynamicAttribute(string $name): bool
    {
        return array_key_exists($name, $this->getDynamicAttributes());
    }

    private function getDynamicAttributeDefaultValue(string $name): mixed
    {
        return $this->getDynamicAttributes()[$name] ?? null;
    }

}