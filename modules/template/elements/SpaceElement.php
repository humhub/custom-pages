<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\elements;

use humhub\modules\space\models\Space;
use humhub\modules\space\widgets\SpacePickerField;
use humhub\modules\ui\form\widgets\ActiveForm;
use Yii;

/**
 * Class to manage content records of the Space elements
 */
class SpaceElement extends BaseContentContainerElement
{
    public const CONTAINER_CLASS = Space::class;

    /**
     * @inheritdoc
     */
    public function getLabel(): string
    {
        return Yii::t('CustomPagesModule.template', 'Space');
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'guid' => Yii::t('CustomPagesModule.template', 'Select space'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getTemplateVariable(): BaseElementVariable
    {
        return SpaceElementVariable::instance($this)->setRecord($this->getRecord());
    }

    /**
     * @inheritdoc
     */
    public function renderEditForm(ActiveForm $form): string
    {
        return $form->field($this, 'guid')->widget(SpacePickerField::class, [
            'minInput' => 2,
            'maxSelection' => 1,
        ]);
    }
}
