<?php

namespace humhub\modules\custom_pages\models;

use humhub\components\ActiveRecord;


/**
 * This is the model class for table "custom_pages_iframe_attr".
 *
 * `attr` are additional iframe attributes such as `allowfullscreen allow="camera; microphone"`
 *
 * @property int $id
 * @property string $object_model
 * @property int $object_id
 * @property string|null $attr
 */
class IframeAttr extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'custom_pages_iframe_attr';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['object_model', 'object_id'], 'required'],
            [['object_id'], 'integer'],
            [['object_model'], 'string', 'max' => 100],
            [['attr'], 'string', 'max' => 255],
        ];
    }
}
