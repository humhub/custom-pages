<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\elements;

use humhub\helpers\Html;
use humhub\modules\post\models\Post;
use humhub\widgets\form\ActiveForm;
use Yii;

/**
 * Class to manage content records of the Post elements
 *
 * @property-read Post|null $record
 */
class PostElement extends BaseContentRecordElement
{
    protected const RECORD_CLASS = Post::class;

    /**
     * @inheritdoc
     */
    public function getLabel(): string
    {
        return Yii::t('PostModule.base', 'Post');
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'contentId' => Yii::t('CustomPagesModule.base', 'Enter post content ID'),
        ];
    }

    public function __toString()
    {
        return Html::encode($this->record?->message);
    }

    /**
     * @inheritdoc
     */
    public function renderEditForm(ActiveForm $form): string
    {
        return $form->field($this, 'contentId')->textInput(['maxlength' => 255])->label(true);
    }
}
