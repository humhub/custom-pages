<?php

namespace humhub\modules\custom_pages\modules\template\models;

use Yii;

/**
 * This is the model class for table "custom_pages_page".
 *
 * The followings are the available columns in table 'custom_pages_page':
 */
class ImageContentDefinition extends ContentDefinition
{

    /**
     * @return string the associated database table name
     */
    public static function tableName()
    {
        return 'custom_pages_template_image_content_definition';
    }

    public function rules()
    {
        return [
            [['style'], 'string'],
            [['height', 'width'], 'integer']
        ];
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'style' => Yii::t('CustomPagesModule.modules_template_models_ImageContentDefinition', 'Style'),
            'height' => Yii::t('CustomPagesModule.modules_template_models_ImageContentDefinition', 'Height'),
            'width' => Yii::t('CustomPagesModule.modules_template_models_ImageContentDefinition', 'Width'),
        ]);
    }
}
