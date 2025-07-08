<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\elements;

use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\user\models\User;
use humhub\modules\user\widgets\UserPickerField;
use humhub\widgets\form\ActiveForm;
use Yii;

/**
 * Class to manage content records of the User elements
 */
class UserElement extends BaseContentContainerElement
{
    public const CONTAINER_CLASS = User::class;

    /**
     * @inheritdoc
     */
    public function getLabel(): string
    {
        return Yii::t('CustomPagesModule.template', 'User');
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'guid' => Yii::t('CustomPagesModule.template', 'Select user'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeHints()
    {
        return [
            'guid' => Yii::t('CustomPagesModule.template', 'When no user is selected, the current logged in user will be used.'),
        ];
    }

    /**
     * @inheritdoc
     *
     * @return User|null
     */
    public function getRecord(?string $guid = null): ?ContentContainerActiveRecord
    {
        return parent::getRecord($this->guid ?: Yii::$app->user->getGuid());
    }

    /**
     * Get a profile field
     *
     * @param string|null $field Field name or NULL to get a display name by default
     * @return string
     */
    public function getProfileField(string $field = null): string
    {
        return $this->getRecord() instanceof User
            ? $this->getRecord()->profile->$field ?? $this->getRecord()->displayName
            : '';
    }

    /**
     * @inheritdoc
     */
    public function isCacheable(): bool
    {
        // Don't cache data of the current user
        // Cache only when specific user is selected
        return !empty($this->guid);
    }

    /**
     * @inheritdoc
     */
    public function renderEditForm(ActiveForm $form): string
    {
        return $form->field($this, 'guid')->widget(UserPickerField::class, [
            'minInput' => 2,
            'maxSelection' => 1,
        ]);
    }
}
